<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;

final class ImportTranslationsCountries extends Command
{
    protected $signature = 'import:translations:countries {file} {--locale=en} {--header}';

    protected $description = 'Imports csv with full country names and iso2 codes';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $file = $this->argument('file');
        $hasHeader = $this->option('header');
        $locale = $this->option('locale');

        $iX = 0;
        $countries = [];
        $handle = fopen($file, 'rb');
        if ($handle !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                if ($hasHeader && $iX++ === 0) {
                    continue;
                }

                [$name, $iso2] = $data;

                $countries[] = ['name' => $name, 'iso2' => $iso2];

                $country = Country::firstOrCreate([
                    'iso2' => $iso2,
                ]);
                $country->save();
            }
            fclose($handle);
        }

        file_put_contents(
            resource_path("lang/{$locale}/countries.php"),
            view('translatable.countries', compact('countries'))
        );

        return 0;
    }
}
