<?php

namespace App\Http\Controllers\Api;

use App\DataTransferObjects\TagData;
use App\Http\Controllers\Controller;
use App\Http\Requests\TagCreateRequest;
use App\Http\Requests\TagUpdateRequest;
use App\Models\Tag;
use App\Models\User;
use App\Services\Repositories\TagRepository;
use App\ViewModels\ApiModels\TagViewModel;

class TagController extends Controller
{
    private TagRepository $repository;

    public function __construct(TagRepository $repository)
    {
        $this->authorizeResource(Tag::class, 'tag');
        $this->repository = $repository;
    }

    public function index(User $user)
    {
        return TagViewModel::fromCollection($user->tags);
    }

    public function show(Tag $tag)
    {
        return new TagViewModel($tag);
    }

    public function update(Tag $tag, TagUpdateRequest $request)
    {
        $this->repository->update($tag, TagData::fromArray($request->all()));

        return new TagViewModel($tag);
    }

    public function store(User $user, TagCreateRequest $request)
    {
        $tag = new Tag();
        $tag->user()->associate($user);
        $this->repository->update($tag, TagData::fromArray($request->all()));

        return new TagViewModel($tag);
    }
}
