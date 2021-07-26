<?php

declare(strict_types=1);

namespace App\Application\Dives\ViewModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Dives\Entities\DiveTank;

final class DiveTankViewModel extends ViewModel
{
    protected array $visible = ['volume', 'oxygen', 'pressure'];

    public function __construct(
        private int $volume,
        private int $oxygen,
        private int $pressure_begin,
        private int $pressure_end,
        private string $pressure_type,
    ) {
    }

    public static function fromDiveTank(DiveTank $diveTank)
    {
        return new self(
            $diveTank->getVolume(),
            $diveTank->getGasMixture()->getOxygen(),
            $diveTank->getPressures()->getBegin(),
            $diveTank->getPressures()->getEnd(),
            $diveTank->getPressures()->getType(),
        );
    }

    public function getVolume()
    {
        return $this->volume;
    }

    public function getOxygen()
    {
        return $this->oxygen;
    }

    public function getPressure()
    {
        return [
            'begin' => $this->pressure_begin,
            'end' => $this->pressure_end,
            'type' => $this->pressure_type,
        ];
    }
}
