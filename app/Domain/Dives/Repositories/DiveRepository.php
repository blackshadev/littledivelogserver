<?php

declare(strict_types=1);

namespace App\Domain\Dives\Repositories;

use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\ValueObjects\DiveId;

interface DiveRepository
{
    public function findById(DiveId $diveId): Dive;

    public function save(Dive $dive): DiveId;

    public function remove(Dive $getDive): void;

    public function findByFingerprint(int $userId, int $computerId, string $fingerprint);
}
