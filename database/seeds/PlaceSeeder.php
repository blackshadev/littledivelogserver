<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    public function run()
    {
        $countries = Country::query()->whereIn('iso2', ['NL', 'GR', 'EG'])->get('iso2');
        $users = User::query();

        Place::factory()
            ->state(fn () => [
                'country_code' => $countries->random(),
                'created_by' => $users->inRandomOrder()->first()->id,
            ])
            ->count(30)
            ->create();

        Place::factory()
            ->state(fn () => [
                'country_code' => $countries->random(),
                'created_by' => null,
            ])
            ->count(10)
            ->create();
    }
}
