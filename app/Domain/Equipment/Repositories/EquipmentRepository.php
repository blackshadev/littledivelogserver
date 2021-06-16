<?php

declare(strict_types=1);

namespace App\Domain\Equipment\Repositories;

use App\Domain\Equipment\DataTransferObjects\EquipmentData;
use App\Domain\Equipment\Entities\Equipment;

interface EquipmentRepository
{
    public function save(Equipment $equipment): void;

    public function setData(Equipment $equipment, EquipmentData $data): void;

    public function forUser(int $userId): Equipment;
}
