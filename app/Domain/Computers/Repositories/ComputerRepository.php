<?php

declare(strict_types=1);

namespace App\Domain\Computers\Repositories;

use App\Domain\Computers\Entities\Computer;
use App\Domain\Users\Entities\User;

interface ComputerRepository
{
    public function findById(int $id): Computer;

    public function findBySerial(User $user, int $serial): ?Computer;

    public function save(Computer $computer): void;
}
