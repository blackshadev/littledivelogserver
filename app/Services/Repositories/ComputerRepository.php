<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\Domain\DataTransferObjects\ComputerData;
use App\Error\ComputerAlreadyExists;
use App\Models\Computer;
use App\Models\User;
use Carbon\Carbon;

class ComputerRepository
{
    public function create(ComputerData $computerData, User $user)
    {
        $serial = $computerData->getSerial();
        $hasComputer = $serial !== null ? $this->findBySerial($serial, $user) : null;
        if ($hasComputer) {
            throw new ComputerAlreadyExists($serial);
        }

        $computer = $this->make($computerData, $user);
        $this->save($computer);

        return $computer;
    }

    public function createOrFind(ComputerData $computerData, User $user)
    {
        $serial = $computerData->getSerial();
        $computer = $this->findBySerial($serial, $user);
        if ($computer !== null) {
            return $computer;
        }

        $computer = $this->make($computerData, $user);
        $this->save($computer);

        return $computer;
    }

    public function find(int $id): ?Computer
    {
        return Computer::find($id);
    }

    public function findBySerial(int $serial, User $user): ?Computer
    {
        return $user->computers()->where('serial', $serial)->first();
    }

    public function updateLastRead(Computer $computer, Carbon $date, string $fingerprint)
    {
        /** @var Carbon $computerDate */
        $computerDate = $computer->last_read;
        if ($computerDate->greaterThanOrEqualTo($date)) {
            return;
        }

        $computer->last_read = $date;
        $computer->last_fingerprint = $fingerprint;

        $this->save($computer);
    }

    public function make(ComputerData $computerData, User $user): Computer
    {
        $computer = Computer::make([
            'serial' => $computerData->getSerial(),
            'name' => $computerData->getName(),
            'model' => $computerData->getModel(),
            'vendor' => $computerData->getVendor(),
            'type' => $computerData->getType(),
        ]);
        $computer->user_id = $user->id;

        return $computer;
    }

    public function save(Computer $computer)
    {
        $computer->save();
    }
}
