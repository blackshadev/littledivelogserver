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
        private ComputerRepository $computerRepository,
        private PlaceProvider $placeProvider,
        private CurrentUserRepository $userRepository,
    ) {
    }

    public function update(Dive $dive, DiveData $diveData): void
    {
        $user = $this->userRepository->getCurrentUser();

        $dive->setDate($diveData->getDate());
        $dive->setMaxDepth($diveData->getMaxDepth());
        $dive->setDivetime($diveData->getDivetime());
        $dive->setFingerprint($diveData->getFingerprint());

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

        $computer = $diveData->getComputerId() !== null ?
            $this->computerRepository->findById($diveData->getComputerId()) : null;
        $dive->setComputer($computer);
        if (!is_null($computer)) {
            $computer->updateLastRead($dive->getDate(), $dive->getFingerprint());
        }

        $place = !$diveData->getPlace()->isEmpty() ?
            $this->placeProvider->findOrMake($user, $diveData->getPlace()) : null;
        $dive->setPlace($place);

        $tanks = Arrg::map(
            $diveData->getTanks(),
            fn (TankData $tank) => DiveTank::new(
                diveId: null,
                volume: $tank->getVolume(),
                gasMixture: new GasMixture(
                    oxygen: $tank->getOxygen(),
                ),
                pressures: new TankPressures(
                    begin: $tank->getPressures()->getBegin(),
                    end: $tank->getPressures()->getEnd(),
                    type: $tank->getPressures()->getType(),
                ),
            )
        );
        $dive->setTanks($tanks);

        $this->diveRepository->save($dive);
    }
}
