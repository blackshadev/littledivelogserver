<?php

declare(strict_types=1);

namespace App\Repositories\Countries;

use App\Domain\Countries\Entity\Country;
use App\Domain\Countries\Repositories\CountryRepository;
use App\Models\Country as CountryModel;

final class EloquentCountryRepository implements CountryRepository
{
    /** @return Country[] */
    public function list(): array
    {
        return CountryModel::all()
            ->map(fn (CountryModel $country) => new Country(
                iso2: $country->iso2,
                name: __('countries.' . $country->iso2)
            ))->toArray();
    }
}
