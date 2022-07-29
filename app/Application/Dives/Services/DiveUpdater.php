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
     * Does not update divecomputer
     */
    public function update(Dive $dive, DiveData $diveData): DiveId
    {
        $user = $this->userRepository->getCurrentUser();

        $dive->setDate($diveData->getDate());
        $dive->setMaxDepth($diveData->getMaxDepth());
        $dive->setDivetime($diveData->getDivetime());

        if ($diveData->getSamples() !== null) {
            $dive->setSamples($diveData->getSamples());
        }

        $buddies = array_map(
            fn (BuddyData $buddy) => $this->buddyProvider->findOrMake($user, $buddy),
            $diveData->getBuddies(),
        );
        $dive->setBuddies($buddies);

        $tags = array_map(
            fn (TagData $tag) => $this->tagProvider->findOrMake($user, $tag),
            $diveData->getTags(),
        );
        $dive->setTags($tags);

        $place = !$diveData->getPlace()->isEmpty() ?
            $this->placeProvider->findOrMake($user, $diveData->getPlace()) : null;
        $dive->setPlace($place);

        $computer = $diveData->getComputerId() !== null ?
            $this->computerRepository->findById($diveData->getComputerId()) : null;
        $dive->setComputer($computer);

        $tanks = Arrg::map(
            $diveData->getTanks(),
            fn (TankData $tank) => DiveTank::new(
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
