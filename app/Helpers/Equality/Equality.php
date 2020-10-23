<?php

declare(strict_types=1);

namespace App\Helpers\Equality;

interface Equality
{
    public function isEqualTo($other): bool;
}
