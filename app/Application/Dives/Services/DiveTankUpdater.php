<?php

declare(strict_types=1);

namespace App\Application\Dives\Services;

use App\Application\Equipment\DataTransferObjects\TankData;
use App\Domain\Dives\Entities\DiveTank;
use App\Domain\Dives\Repositories\DiveTankRepository;
use App\Domain\Dives\ValueObjects\GasMixture;
use App\Domain\Dives\ValueObjects\TankPressures;

final class DiveTankUpdater
{
    public function __construct(
        private DiveTankRepository $diveTankRepository
    ) {
    }

    public function update(DiveTank $diveTank, TankData $data): void
    {
        $diveTank->setGasMixture(new GasMixture($data->getOxygen()));
        $diveTank->setPressures(
            new TankPressures(
                type: $data->getPressures()->getType(),
                begin: $data->getPressures()->getBegin(),
                end: $data->getPressures()->getEnd(),
            )
        );
        $diveTank->setVolume($data->getVolume());
        $this->diveTankRepository->save($diveTank);
    }
}
