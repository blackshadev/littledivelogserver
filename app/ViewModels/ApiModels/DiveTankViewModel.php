<?php

declare(strict_types=1);

namespace App\ViewModels\ApiModels;

use App\Models\DiveTank;
use App\ViewModels\FromEloquentCollection;
use App\ViewModels\ViewModel;

class DiveTankViewModel extends ViewModel
{
    use FromEloquentCollection;

    protected array $visible = ['volume', 'oxygen', 'pressure'];

    private $tank;

    public function __construct(DiveTank $tank)
    {
        $this->tank = $tank;
    }

    public function getVolume()
    {
        return $this->tank->volume;
    }

    public function getOxygen()
    {
        return $this->tank->oxygen;
    }

    public function getPressure()
    {
        return [
            'begin' => $this->tank->pressure_begin,
            'end' => $this->tank->pressure_end,
            'type' => $this->tank->pressure_type,
        ];
    }
}
