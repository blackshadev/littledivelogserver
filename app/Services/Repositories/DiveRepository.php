<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\CommandObjects\FindDivesCommand;
use App\Domain\Buddies\DataTransferObjects\BuddyData;
use App\Domain\DataTransferObjects\DiveData;
use App\Domain\DataTransferObjects\NewDiveData;
use App\Domain\Equipment\DataTransferObjects\TankData;
use App\Domain\Support\Arrg;
use App\Domain\Tags\DataTransferObjects\TagData;
use App\Error\ComputerNotFound;
use App\Models\Buddy;
use App\Models\Dive;
use App\Models\DiveTank;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JeroenG\Explorer\Application\IndexAdapterInterface;
use JeroenG\Explorer\Application\Results;
use JeroenG\Explorer\Application\SearchCommand;
use JeroenG\Explorer\Domain\Query\Query;
use JeroenG\Explorer\Domain\Syntax\Compound\BoolQuery;
use JeroenG\Explorer\Domain\Syntax\Matching;
use JeroenG\Explorer\Domain\Syntax\Nested;
use JeroenG\Explorer\Domain\Syntax\Range;
use JeroenG\Explorer\Domain\Syntax\Sort;
use JeroenG\Explorer\Domain\Syntax\Term;

class DiveRepository
{
    private PlaceRepository $placeRepository;

    private BuddyRepository $buddyRepository;

    private TagRepository $tagRepository;

    private DiveTankRepository $tankRepository;

    private ComputerRepository $computerRepository;

    private IndexAdapterInterface $searchAdapter;

    public function __construct(
        PlaceRepository $placeRepository,
        BuddyRepository $buddyRepository,
        TagRepository $tagRepository,
        DiveTankRepository $tankRepository,
        ComputerRepository $computerRepository,
        IndexAdapterInterface $searchAdapter
    ) {
        $this->placeRepository = $placeRepository;
        $this->buddyRepository = $buddyRepository;
        $this->tagRepository = $tagRepository;
        $this->tankRepository = $tankRepository;
        $this->computerRepository = $computerRepository;
        $this->searchAdapter = $searchAdapter;
    }

    public function update(Dive $dive, DiveData $data)
    {
        DB::transaction(function () use ($dive, $data) {
            $dive->date = $data->getDate();
            $dive->max_depth = $data->getMaxDepth();
            $dive->divetime = $data->getDivetime();

            if ($data->getSamples()) {
                $dive->samples = json_encode($data->getSamples());
            }

            if (!$data->getPlace()->isEmpty()) {
                $place = $this->placeRepository->findOrCreate($data->getPlace(), $dive->user);
                $dive->place()->associate($place);
            } else {
                $dive->place()->dissociate();
            }

            if ($data->getComputerId() !== null) {
                $computer = $this->computerRepository->find($data->getComputerId());
                if ($computer === null) {
                    throw new ComputerNotFound();
                }

                $dive->computer()->associate($computer);

                if ($data->getFingerprint() !== null) {
                    $this->computerRepository->updateLastRead($computer, $data->getDate(), $data->getFingerprint());
                }
            }

            // Ensures dive exists before we attach other relations to it
            if ($data instanceof NewDiveData) {
                $dive->user()->associate($data->getUser()->id);
                $this->save($dive);
            }

            if ($data->getTags() !== null) {
                /** @var TagData $tag */
                $tags = array_map(
                    fn ($tag) => $this->tagRepository->findOrCreate($tag, $dive->user),
                    $data->getTags()
                );

                $this->attachTags($dive, $tags);
            }

            if ($data->getBuddies() !== null) {
                /** @var BuddyData $buddy */
                $buddies = array_map(
                    fn ($buddy) => $this->buddyRepository->findOrCreate($buddy, $dive->user),
                    $data->getBuddies()
                );

                $this->attachBuddies($dive, $buddies);
            }

            if ($data->getTanks() !== null) {
                $this->updateDiveTanks($dive, $data->getTanks());
            }

            $this->save($dive);
        });
    }

    public function save(Dive $dive)
    {
        $dive->save();
    }

    /** @param Tag[] $tags */
    public function attachTags(Dive $dive, array $tags)
    {
        $dive->tags()->sync(array_map(fn ($tag) => $tag->id, $tags));
    }

    /** @param Buddy[] $dive */
    public function attachBuddies(Dive $dive, array $buddies)
    {
        $dive->buddies()->sync(array_map(fn ($buddy) => $buddy->id, $buddies));
    }

    public function appendTank(Dive $dive, DiveTank $tank)
    {
        $dive->tanks()->save($tank);
    }

    public function removeTank(Dive $dive, DiveTank $tank)
    {
        $this->tankRepository->delete($tank);
    }

    /**
     * @param Dive[] $dives
     */
    public function removeMany(array $dives)
    {
        Dive::destroy(Arrg::map($dives, fn ($dive) => $dive->id));
    }

    /** @return Dive[] */
    public function find(FindDivesCommand $findDivesCommand): Collection
    {
        $toplevelQuery = new BoolQuery();
        $toplevelQuery->filter(new Term('user_id', $findDivesCommand->getUserId()));

        if ($findDivesCommand->getKeywords()) {
            $keywordQuery = new BoolQuery();
            $keywordQuery->should(new Nested('place', new Matching('place.name', $findDivesCommand->getKeywords())));
            $keywordQuery->should(new Nested('buddies', new Matching('buddies.name', $findDivesCommand->getKeywords())));
            $keywordQuery->should(new Nested('tags', new Matching('tags.text', $findDivesCommand->getKeywords())));
            $toplevelQuery->must($keywordQuery);
        }

        if ($findDivesCommand->getAfter() !== null) {
            $toplevelQuery->must(new Range('date', [
                'gt' => $findDivesCommand->getAfter()
            ]));
        }
        if ($findDivesCommand->getBefore() !== null) {
            $toplevelQuery->must(new Range('date', [
                'lt' => $findDivesCommand->getBefore()
            ]));
        }
        if ($findDivesCommand->getPlaceId() !== null) {
            $toplevelQuery->must(new Nested('place', new Term('place.id', $findDivesCommand->getPlaceId())));
        }
        if ($findDivesCommand->getBuddies() !== null) {
            foreach ($findDivesCommand->getBuddies() as $buddyId) {
                $toplevelQuery->must(new Nested('buddies', new Term('buddies.id', $buddyId)));
            }
        }
        if ($findDivesCommand->getTags() !== null) {
            foreach ($findDivesCommand->getTags() as $tagId) {
                $toplevelQuery->must(new Nested('tags', new Term('tags.id', $tagId)));
            }
        }

        $searchQuery = new Query();
        $searchQuery->setQuery($toplevelQuery);
        $searchQuery->setSort([new Sort('date', 'desc')]);
        $index = (new Dive())->searchableAs();

        $results = $this->searchAdapter->search(new SearchCommand($index, $searchQuery));

        return $this->mapResultsResult($results);
    }

    public function findOrMake(User $user, ?string $fingerprint)
    {
        if ($fingerprint === null) {
            return new Dive();
        }

        $dive = $user->dives()->where('fingerprint', $fingerprint)->first();
        if ($dive !== null) {
            return $dive;
        }

        return  new Dive();
    }

    /**
     * @param int[] $ids
     * @return Collection
     */
    public function findByIds(Collection $ids): Collection
    {
        return Dive::whereIn('id', $ids)->get();
    }

    /** @param TankData[] $tanks */
    protected function updateDiveTanks(Dive $dive, array $tanks)
    {
        $iX = 0;
        /** @var DiveTank $tank */
        foreach ($dive->tanks as $tank) {
            $tankData = $tanks[$iX++] ?? null;
            if ($tankData === null) {
                $this->removeTank($dive, $tank);
            } else {
                $this->tankRepository->update($tank, $tankData);
            }
        }

        for ($iXMax = count($tanks); $iX < $iXMax; $iX++) {
            $tankData = $tanks[$iX];
            $tank = $this->tankRepository->make($tankData);
            $this->appendTank($dive, $tank);
        }
        $dive->unsetRelation('tanks');
    }

    /** @return Collection */
    private function mapResultsResult(Results $results): Collection
    {
        $ids = collect($results->hits())->pluck('_id')->values();
        return $this->findByIds($ids);
    }
}
