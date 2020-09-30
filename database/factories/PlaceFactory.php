<?php

namespace Database\Factories;

use App\Models\Country;
use \Illuminate\Database\Eloquent\Factories\Factory;

class PlaceFactory extends Factory
{

    public function definition()
    {
        return [
            'country_code' => Country::all('iso2')->random()->iso2,
            'name' => $this->faker->city
        ];
    }
}
