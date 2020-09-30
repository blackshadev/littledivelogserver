<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\ViewModels\ApiModels\TranslatedCountryViewModel;
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
