<?php

declare(strict_types=1);

namespace App\Domain\Dives\ValueObjects;

final class GasMixture
{
    public function __construct(
        private ?int $oxygen,
    ) {
    }

    public function getOxygen(): ?int
    {
        return $this->oxygen;
    }
}
