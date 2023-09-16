<?php

declare(strict_types=1);

namespace App\Application\Dives\Services;

use App\Application\Equipment\DataTransferObjects\TankData;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Entities\DiveTank;
use App\Domain\Dives\Repositories\DiveTankRepository;
use App\Domain\Dives\ValueObjects\GasMixture;
use App\Domain\Dives\ValueObjects\TankPressures;

final class DiveTankCreator
{
    public function __construct(
        private DiveTankRepository $diveTankRepository,
    ) {
    }

    public function create(Dive $dive, TankData $data): DiveTank
    {
        $diveTank = DiveTank::new(
            diveId: $dive->getDiveId()->value(),
            volume: $data->getVolume(),
            gasMixture: new GasMixture(
                oxygen: $data->getOxygen()
            ),
            pressures: new TankPressures(
                type: $data->getPressures()->getType(),
                begin: $data->getPressures()->getBegin(),
                end: $data->getPressures()->getEnd(),
            )
        );
        $this->diveTankRepository->save($diveTank);

        return $diveTank;
    }
}
