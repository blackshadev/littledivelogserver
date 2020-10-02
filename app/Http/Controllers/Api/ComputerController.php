<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Computer;
use App\Models\User;
use App\ViewModels\ApiModels\ComputerListViewModel;
use Illuminate\Http\Request;

class ComputerController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Computer::class, 'computer');
    }

    public function index(User $user)
    {
        return ComputerListViewModel::fromCollection($user->computers);
    }

    public function show(Computer $computer)
    {
        return new ComputerListViewModel($computer);
    }

}
