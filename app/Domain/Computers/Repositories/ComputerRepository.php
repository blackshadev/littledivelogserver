<?php

declare(strict_types=1);

namespace App\Domain\Computers\Repositories;

use App\Domain\Computers\Entities\Computer;

interface ComputerRepository
{
    public function findById(int $id): Computer;

    public function save(Computer $computer): void;
}
