<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\FromEloquentCollection;
use App\Application\ViewModels\ViewModel;
use App\Models\Country;

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
