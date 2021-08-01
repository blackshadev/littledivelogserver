<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

final class CountrySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('countries')->truncate();

        Artisan::call('import:translations:countries --locale=en --header ' . base_path('imports/countries_en.csv'));
    }
}
