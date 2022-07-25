<?php

declare(strict_types=1);

namespace App\Domain\DiveSamples\Visitors;

use App\Domain\DiveSamples\Entities\DiveSampleAccessor;

interface DiveSampleVisitor
{
    public function visit(DiveSampleAccessor $diveSamples): array;
}
