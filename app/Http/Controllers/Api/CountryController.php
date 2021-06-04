<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\ViewModels\ApiModels\TranslatedCountryViewModel;
use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Support\Collection;

class CountryController extends Controller
{
    public function index()
    {
        /** @var Collection $all */
        $all = Country::all();

        return TranslatedCountryViewModel::fromCollection($all);
    }
}
