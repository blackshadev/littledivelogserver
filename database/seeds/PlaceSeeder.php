<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Place;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    public function run()
    {
        $countries = Country::query()->whereIn('iso2', ['NL', 'GR', 'EG'])->get('iso2');
        Place::factory()
            ->state(fn () => [ 'country_code' => $countries->random() ])
            ->count(50)
            ->create();
    }
}
