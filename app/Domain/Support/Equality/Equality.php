<?php

declare(strict_types=1);

namespace App\Domain\Support\Equality;

interface Equality
{
    public function isEqualTo($other): bool;
}
