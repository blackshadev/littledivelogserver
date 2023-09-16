<?php

declare(strict_types=1);

namespace App\Application\Dives\Services;

use App\Application\Buddies\DataTransferObjects\BuddyData;
use App\Application\Buddies\Services\BuddyProvider;
use App\Application\Dives\DataTransferObjects\DiveData;
use App\Application\Equipment\DataTransferObjects\TankData;
use App\Application\Places\Services\PlaceProvider;
use App\Application\Tags\DataTransferObjects\TagData;
use App\Application\Tags\Services\TagProvider;
use App\Domain\Computers\Repositories\ComputerRepository;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Entities\DiveTank;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\Dives\ValueObjects\GasMixture;
use App\Domain\Dives\ValueObjects\TankPressures;
use App\Domain\Support\Arrg;
use App\Domain\Users\Repositories\CurrentUserRepository;

final class DiveUpdater
{
    public function __construct(
        private DiveRepository $diveRepository,
        private BuddyProvider $buddyProvider,
        private TagProvider $tagProvider,
        private PlaceProvider $placeProvider,
        private CurrentUserRepository $userRepository,
        private ComputerRepository $computerRepository,
    ) {
    }

    /**
     * Updates all dive attributes except:
     *  - fingerprint
     * Note: Does not update divecomputer's last read and fingerprint
     */
    public function update(Dive $dive, DiveData $diveData): DiveId
    {
        $user = $this->userRepository->getCurrentUser();

        $dive->setDate($diveData->date);
        $dive->setMaxDepth($diveData->maxDepth);
        $dive->setDivetime($diveData->divetime);

        if ($diveData->samples !== null) {
            $dive->setSamples($diveData->samples);
        }

        $buddies = array_map(
            fn (BuddyData $buddy) => $this->buddyProvider->findOrMake($user, $buddy),
            $diveData->buddies,
        );
        $dive->setBuddies($buddies);

        $tags = array_map(
            fn (TagData $tag) => $this->tagProvider->findOrMake($user, $tag),
            $diveData->tags,
        );
        $dive->setTags($tags);

        $place = !$diveData->placeData->isEmpty() ?
            $this->placeProvider->findOrMake($user, $diveData->placeData) : null;
        $dive->setPlace($place);

        if (!$dive->getFingerprint()) {
            $computer = $diveData->computerId !== null ?
                $this->computerRepository->findById($diveData->computerId) : null;
            $dive->setComputer($computer);
        }

        $tanks = Arrg::map(
            $diveData->tanks,
            static fn (TankData $tank) => DiveTank::new(
                diveId: null,
                volume: $tank->getVolume(),
                gasMixture: new GasMixture(
                    oxygen: $tank->getOxygen(),
                ),
                pressures: new TankPressures(
                    type: $tank->getPressures()->getType(),
                    begin: $tank->getPressures()->getBegin(),
                    end: $tank->getPressures()->getEnd(),
                ),
            )
        );
        $dive->setTanks($tanks);

        return $this->diveRepository->save($dive);
    }
}
