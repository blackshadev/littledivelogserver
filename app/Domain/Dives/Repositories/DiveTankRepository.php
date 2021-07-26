<?php

declare(strict_types=1);

namespace App\Domain\Dives\Repositories;

use App\Domain\Dives\Entities\DiveTank;

interface DiveTankRepository
{
    public function findById(int $id): DiveTank;

    public function save(DiveTank $diveTank): void;

    public function remove(DiveTank $diveTank): void;
}
