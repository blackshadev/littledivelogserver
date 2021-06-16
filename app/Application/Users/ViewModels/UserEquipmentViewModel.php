<?php

declare(strict_types=1);

namespace App\Application\Users\ViewModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Equipment\Entities\Equipment;
use App\Domain\Equipment\Entities\Tank;
use App\Domain\Support\Arrg;

class UserEquipmentViewModel extends ViewModel
{
    protected array $visible = [
        'tanks',
    ];

    public function __construct(
        private Equipment $equipment
    ) {
    }

    public function getTanks(): array
    {
        $tanks = $this->equipment->getTanks();

        return Arrg::map($tanks, fn (Tank $tank) => [
            'volume' => $tank->getVolume(),
            'oxygen' => $tank->getOxygen(),
            'pressure' => [
                'begin' => $tank->getBeginPressure(),
                'end' => $tank->getEndPressure(),
                'type' => $tank->getPressureType(),
            ]
        ]);
    }
}
