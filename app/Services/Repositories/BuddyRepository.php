<?php

namespace App\Services\Repositories;

use App\DataTransferObjects\BuddyData;
use App\Error\BuddyNotFound;
use App\Helpers\Color;
use App\Models\Buddy;
use App\Models\User;
use App\Rules\HexColor;
use Illuminate\Database\Eloquent\Builder;

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
        $buddy = $user->buddies()->find($id);
        return $buddy;
    }

    public function findByName(string $name, User $user): ?Buddy
    {
        /** @var Buddy|null $buddy */
        $buddy = $user->buddies()->find([
            'name' => $name
        ]);
        return $buddy;
    }

    public function save(Buddy $buddy)
    {
        $buddy->save();
    }
}
