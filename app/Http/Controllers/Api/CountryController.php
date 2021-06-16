<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\ViewModels\ApiModels\TranslatedCountryViewModel;
use App\Domain\Countries\Entity\Country;
use App\Domain\Countries\Repositories\CountryRepository;
use App\Domain\Support\Arrg;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    public function __construct(
        private CountryRepository $repository
    ) {
    }

    public function index()
    {
        $countries = $this->repository->list();

        return Arrg::map(
            $countries,
            fn (Country $country) => TranslatedCountryViewModel::fromCountry($country)
        );
    }
}
