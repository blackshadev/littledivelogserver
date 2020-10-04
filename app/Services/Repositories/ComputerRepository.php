<?php


namespace App\Services\Repositories;


use App\DataTransferObjects\ComputerData;
use App\Error\ComputerAlreadyExists;
use App\Models\Computer;
use App\Models\User;
use Carbon\Carbon;

class ComputerRepository
{
    public function create(ComputerData $computerData, User $user)
    {
        $hasComputer = $user->computers()->where('serial', $computerData->getSerial())->exists();
        if ($hasComputer) {
            throw new ComputerAlreadyExists($computerData->getSerial());
        }

        Computer::create([
            'serial' => $computerData->getSerial(),
            'name' => $computerData->getName(),
            'model' => $computerData->getModel(),
            'vendor' => $computerData->getVendor(),
            'type' => $computerData->getType(),
            'user_id' => $user->id
        ]);
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
    }
}
