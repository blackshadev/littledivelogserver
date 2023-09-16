<?php

declare(strict_types=1);

namespace App\Application\Dives\Services;

use App\Application\Dives\DataTransferObjects\DiveData;
use App\Application\Equipment\DataTransferObjects\TankData;
use App\Domain\Computers\Repositories\ComputerRepository;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Entities\DiveTank;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\Dives\ValueObjects\GasMixture;
use App\Domain\Dives\ValueObjects\TankPressures;
use App\Domain\Support\Arrg;
use App\Domain\Users\Entities\User;
use Webmozart\Assert\Assert;

final class DiveComputerDataPatcher
{
    public function __construct(
        private DiveRepository $diveRepository,
        private ComputerRepository $computerRepository,
        private DiveCreatorInterface $creator,
    ) {
    }

    public function patchOrCreate(User $user, DiveData $diveData): DiveId
    {
        $dive = $this->diveRepository->findByFingerprint(
            $user->getId(),
            $diveData->computerId,
            $diveData->fingerprint
        );

        if ($dive === null) {
            return $this->creator->create($user, $diveData);
        }

        return $this->patch($dive, $diveData);
    }

    /**
     * Patches all given dive attributes except the data unavailable from dive computers:
     *  - buddies
     *  - tags
     *  - places
     */
    public function patch(Dive $dive, DiveData $diveData): DiveId
    {
        Assert::notNull($diveData->date);
        Assert::notNull($diveData->maxDepth);
        Assert::notNull($diveData->divetime);
        Assert::notNull($diveData->computerId);
        Assert::notNull($diveData->fingerprint);

        $computer = $this->computerRepository->findById($diveData->computerId);

        $dive->setDate($diveData->date);
        $dive->setDivetime($diveData->divetime);
        $dive->setMaxDepth($diveData->maxDepth);
        $dive->setFingerprint($diveData->fingerprint);
        $dive->setComputer($computer);

        $computer->updateLastRead($dive->getDate(), $dive->getFingerprint());

        if ($diveData->samples !== null) {
            $dive->setSamples($diveData->samples);
        }

        if ($diveData->tanks !== null) {
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
        }

        return $this->diveRepository->save($dive);
    }
}
