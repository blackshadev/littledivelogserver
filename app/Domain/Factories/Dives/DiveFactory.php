<?php

declare(strict_types=1);

namespace App\Domain\Factories\Dives;

use App\Domain\Dives\Entities\Dive;

interface DiveFactory
{
    public function createFrom($model): Dive;
}
