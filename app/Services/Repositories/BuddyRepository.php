<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\DataTransferObjects\BuddyData;
use App\Error\BuddyNotFound;
use App\Models\Buddy;
use App\Models\User;

class BuddyRepository
{
    public function findOrCreate(BuddyData $data, User $user): Buddy
    {
        if ($data->getId()) {
            $buddy = $this->find($data->getId(), $user);
            if ($buddy === null) {
                throw new BuddyNotFound();
            }

            return $buddy;
        }

        if ($data->getName()) {
            $buddy = $this->findByName($data->getName(), $user);

            if ($buddy !== null) {
                return $buddy;
            }

            return $this->create($data, $user);
        }

        throw new \RuntimeException('Buddies data encountered without id or name');
    }

    public function update(Buddy $buddy, BuddyData $data)
    {
        $buddy->fill([
            'name' => $data->getName(),
            'color' => $data->getColor(),
        ]);
        $this->save($buddy);
    }

    public function create(BuddyData $buddyData, User $user): Buddy
    {
        $buddy = new Buddy();
        $buddy->fill([
            'name' => $buddyData->getName(),
            'color' => $buddyData->getColor(),
        ]);
        $buddy->user()->associate($user);
        $this->save($buddy);

        return $buddy;
    }

    public function find(int $id, User $user): ?Buddy
    {
        /** @var Buddy|null $buddy */
        return $user->buddies()->find($id);
    }

    public function findByName(string $name, User $user): ?Buddy
    {
        /** @var Buddy|null $buddy */
        return $user->buddies()->find([
            'name' => $name,
        ]);
    }

    public function save(Buddy $buddy)
    {
        $buddy->save();
    }
}
