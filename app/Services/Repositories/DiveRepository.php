<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\DataTransferObjects\BuddyData;
use App\DataTransferObjects\DiveData;
use App\DataTransferObjects\NewDiveData;
use App\DataTransferObjects\TagData;
use App\DataTransferObjects\TankData;
use App\Error\ComputerNotFound;
use App\Helpers\Arrg;
use App\Models\Buddy;
use App\Models\Dive;
use App\Models\DiveTank;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class DiveRepository
{
    private PlaceRepository $placeRepository;

    private BuddyRepository $buddyRepository;

    private TagRepository $tagRepository;

    private TankRepository $tankRepository;

    private ComputerRepository $computerRepository;

    public function __construct(
        PlaceRepository $placeRepository,
        BuddyRepository $buddyRepository,
        TagRepository $tagRepository,
        TankRepository $tankRepository,
        ComputerRepository $computerRepository
    ) {
        $this->placeRepository = $placeRepository;
        $this->buddyRepository = $buddyRepository;
        $this->tagRepository = $tagRepository;
        $this->tankRepository = $tankRepository;
        $this->computerRepository = $computerRepository;
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
}
