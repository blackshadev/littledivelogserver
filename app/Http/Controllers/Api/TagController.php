<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\ViewModels\ApiModels\TagViewModel;
use App\Domain\Support\Arrg;
use App\Domain\Tags\DataTransferObjects\TagData;
use App\Domain\Tags\Entities\DetailTag;
use App\Domain\Tags\Repositories\DetailTagRepository;
use App\Domain\Tags\Repositories\TagRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tags\TagCreateRequest;
use App\Http\Requests\Tags\TagRequest;
use App\Http\Requests\Tags\TagUpdateRequest;
use App\Models\User;

class TagController extends Controller
{
    public function __construct(
        private TagRepository $repository,
        private DetailTagRepository $detailTagRepository
    ) {
    }

    public function index(User $user)
    {
        return Arrg::map(
            $this->detailTagRepository->listForUser($user->id),
            fn (DetailTag $tag) => TagViewModel::fromDetailTag($tag)
        );
    }

    public function show(TagRequest $request)
    {
        $detailTag = $this->detailTagRepository->findById($request->getTagId());

        return TagViewModel::fromDetailTag($detailTag);
    }

    public function update(TagUpdateRequest $request)
    {
        $tagData = TagData::fromArray($request->all());

        $tag = $request->getTag();

        $this->repository->setData($tag, $tagData);
        $this->repository->save($tag);

        $detailTag = $this->detailTagRepository->findById($tag->getId());
        return TagViewModel::fromDetailTag($detailTag);
    }

    public function store(User $user, TagCreateRequest $request)
    {
        $tagData = TagData::fromArray($request->all());

        $tag = $this->repository->create($user->id, $tagData);
        $this->repository->save($tag);

        $detailTag = $this->detailTagRepository->findById($tag->getId());
        return TagViewModel::fromDetailTag($detailTag);
    }
}
