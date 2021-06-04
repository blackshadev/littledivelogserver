<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\FromEloquentCollection;
use App\Application\ViewModels\ViewModel;
use App\Models\DiveTank;

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
