<?php

declare(strict_types=1);

namespace App\Domain\Countries\Entity;

class Country
{
    public function __construct(
        private string $iso2,
        private string $name,
    ) {
    }

    public function getIso2(): string
    {
        return $this->iso2;
    }

    public function setIso2(string $iso2): void
    {
        $this->iso2 = $iso2;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
