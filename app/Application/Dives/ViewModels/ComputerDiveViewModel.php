<?php

declare(strict_types=1);

namespace App\Application\Dives\ViewModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Computers\Entities\Computer;

final class ComputerDiveViewModel extends ViewModel
{
    protected array $visible = [
        'name', 'vendor'
    ];

    private function __construct(
        private string $name,
        private string $vendor,
    ) {
    }

    public static function fromComputer(Computer $computer): self
    {
        return new self(
            $computer->getName(),
            $computer->getVendor(),
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }
}
