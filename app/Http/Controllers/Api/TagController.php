<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\User;
use App\ViewModels\ApiModels\TagViewModel;

class TagController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Tag::class, 'tag');
    }

    public function index(User $user)
    {
        return TagViewModel::fromCollection($user->tags);
    }

    public function show(Tag $tag)
    {
        return new TagViewModel($tag);
    }
}
