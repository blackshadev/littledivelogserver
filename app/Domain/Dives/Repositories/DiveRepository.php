<?php

declare(strict_types=1);

namespace App\Domain\Dives\Repositories;

use App\Domain\Dives\Entities\Dive;

interface DiveRepository
{
    public function findById(int $diveId): Dive;

    public function save(Dive $dive): void;

    public function remove(Dive $getDive): void;
}
