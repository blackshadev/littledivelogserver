<?php

declare(strict_types=1);

namespace App\ViewModels\ApiModels;

use App\Models\Equipment;
use App\ViewModels\ViewModel;

class UserEquipmentViewModel extends ViewModel
{
    protected array $visible = [
        'tanks',
    ];

    protected Equipment $equipment;

    public function __construct(Equipment $equipment)
    {
        $this->equipment = $equipment;
    }

    public function getTanks()
    {
        $tanks = $this->equipment->tanks;
        return array_map(function ($tank) {
            return [
                'volume' => $tank->volume,
                'oxygen' => $tank->oxygen,
                'pressure' => [
                    'begin' => $this->tank->pressure_begin,
                    'end' => $this->tank->pressure_end,
                    'type' => $this->tank->pressure_type,
                ]
            ];
        }, $tanks);
    }
}
