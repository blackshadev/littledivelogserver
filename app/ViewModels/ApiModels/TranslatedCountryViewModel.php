<?php

declare(strict_types=1);

namespace App\ViewModels\ApiModels;

use App\Models\Country;
use App\ViewModels\FromEloquentCollection;
use App\ViewModels\ViewModel;

final class TranslatedCountryViewModel extends ViewModel
{
    use FromEloquentCollection;

    protected array $visible = ['name', 'iso2'];

    private Country $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    public function getName()
    {
        return __('countries.' . $this->country->iso2);
    }

    public function getIso2()
    {
        return $this->country->iso2;
    }
}
