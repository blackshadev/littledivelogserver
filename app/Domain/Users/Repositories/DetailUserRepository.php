<?php

declare(strict_types=1);

namespace App\Domain\Users\Repositories;

use App\Domain\Users\ViewModel\DetailUser;

interface DetailUserRepository
{
    public function findById(int $id): DetailUser;
}
