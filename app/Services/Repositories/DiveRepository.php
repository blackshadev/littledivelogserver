<?php

namespace App\Services\Repositories;

use App\DataTransferObjects\BuddyData;
use App\DataTransferObjects\DiveData;
use App\DataTransferObjects\TagData;
use App\DataTransferObjects\TankData;
use App\Models\Computer;
use App\Models\Dive;
use App\Models\DiveTank;
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

            if ($data->getComputerId() !== null) {
                $computer = Computer::findOrFail($data->getComputerId());
                $dive->computer()->associate($computer);

                $this->computerRepository->updateLastRead($computer, $data->getDate(), $data->getFingerprint());
            }

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
