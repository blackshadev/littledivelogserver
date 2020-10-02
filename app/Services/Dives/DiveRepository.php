<?php

namespace App\Services\Dives;

use App\DataTransferObjects\BuddyData;
use App\DataTransferObjects\DiveData;
use App\DataTransferObjects\PlaceData;
use App\DataTransferObjects\TagData;
use App\DataTransferObjects\TankData;
use App\Helpers\Color;
use App\Models\Buddy;
use App\Models\Dive;
use App\Models\DiveTank;
use App\Models\Place;
use App\Models\Tag;
use App\Models\User;
use App\Services\Buddies\BuddyRepository;
use App\Services\Places\PlaceRepository;
use App\Services\Tags\TagRepository;
use App\Services\Tanks\TankRepository;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DiveRepository
{

    private PlaceRepository $placeRepository;
    private BuddyRepository $buddyRepository;
    private TagRepository $tagRepository;
    private TankRepository $tankRepository;

    public function __construct(
        PlaceRepository $placeRepository,
        BuddyRepository $buddyRepository,
        TagRepository $tagRepository,
        TankRepository $tankRepository
    ) {
        $this->placeRepository = $placeRepository;
        $this->buddyRepository = $buddyRepository;
        $this->tagRepository = $tagRepository;
        $this->tankRepository = $tankRepository;
    }

    public function update(Dive $dive, DiveData $data)
    {
        DB::transaction(function() use ($dive, $data) {
            $dive->max_depth = $data->getMaxDepth();

            if (!$data->getPlace()->isEmpty()) {
                $place = $this->placeRepository->findOrCreate($data->getPlace());
                $dive->place()->associate($place);
            } else {
                $dive->place()->dissociate();
            }

            /** @var TagData $tag */
            $tags = array_map(
                fn ($tag) => $this->tagRepository->findOrCreate($tag, $dive->user)->id,
                $data->getTags()
            );
            $dive->tags()->sync($tags);

            /** @var BuddyData $buddy */
            $buddies = array_map(
                fn ($buddy) => $this->buddyRepository->findOrCreate($buddy, $dive->user)->id,
                $data->getBuddies()
            );
            $dive->buddies()->sync($buddies);

            $this->updateDiveTanks($dive, $data->getTanks());


            $dive->save();
        });
    }

    /** @param TankData[] $tanks */
    protected function updateDiveTanks(Dive $dive, array $tanks)
    {
        $iX = 0;
        /** @var DiveTank $tank */
        foreach ($dive->tanks as $tank) {
            $tankData = $tanks[$iX++] ?? null;
            if ($tankData === null) {
                $tank->delete();
            } else {
                $this->tankRepository->update($tank, $tankData);
            }
        }

        for (; $iX < count($tanks); $iX++) {
            $tankData = $tanks[$iX];
            $tank = $this->tankRepository->create($tankData);
            $dive->tanks()->save($tank);
        }
        $dive->unsetRelation('tanks');
    }

}
