<?php

declare(strict_types=1);

namespace App\ViewModels\ApiModels;

use App\Models\Equipment;
use App\ViewModels\ViewModel;
use Illuminate\Database\Eloquent\Collection;

class UserEquipmentViewModel extends ViewModel
{
    protected array $visible = [
        'tanks',
    ];

    protected ?Equipment $equipment;

    public function __construct(?Equipment $equipment)
    {
        $this->equipment = $equipment;
    }

    public function getTanks()
    {
        if ($this->equipment === null) {
            return [];
        }

        /** @var Collection $tanks */
        $tanks = $this->equipment->tanks;

        return $tanks->map(function ($tank) {
            return [
                'volume' => $tank->volume,
                'oxygen' => $tank->oxygen,
                'pressure' => [
                    'begin' => $tank->pressure_begin,
                    'end' => $tank->pressure_end,
                    'type' => $tank->pressure_type,
                ]
            ];
        })->toArray();
    }
}
