<?php


namespace App\Services\Repositories;


use App\DataTransferObjects\BuddyData;
use App\Helpers\Color;
use App\Models\Buddy;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BuddyRepository
{
    public function findOrCreate(BuddyData $data, ?User $user = null): Buddy
    {
        /** @var Buddy|Builder $scope */
        $scope = $user !== null ? $user->buddies() : Buddy::query();

        if ($data->getId()) {
            return $scope->findOrFail($data->getId());
        }

        if ($data->getName()) {
            return $scope->firstOrCreate([
                'text' => $data->getName(),
                'color' => $data->getColor() ?? Color::randomHex(),
                'user_id' => $user->id
            ]);
        }

        throw new \RuntimeException("Buddies data encountered without id or name");
    }

    public function update(Buddy $buddy, BuddyData $data)
    {
        $buddy->fill([
            'name' => $data->getName(),
            'color' => $data->getColor()
        ]);
        $buddy->save();
    }
}
