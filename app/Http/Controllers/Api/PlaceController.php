<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Buddy;
use App\Models\Place;
use App\Models\User;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Place::class, 'place');
    }

    public function index(User $user, Request $request)
    {
        $search = $request->query('_search');

    }


}
