<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Tags\DataTransferObjects\TagData;
use App\Application\Tags\Services\TagCreator;
use App\Application\Tags\Services\TagUpdater;
use App\Application\Tags\ViewModels\TagViewModel;
use App\Domain\Support\Arrg;
use App\Domain\Tags\Entities\DetailTag;
use App\Domain\Tags\Repositories\DetailTagRepository;
use App\Domain\Tags\Repositories\TagRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tags\ShowTagRequest;
use App\Http\Requests\Tags\TagCreateRequest;
use App\Http\Requests\Tags\TagDeleteRequest;
use App\Http\Requests\Tags\TagUpdateRequest;
use App\Models\User;

final class TagController extends Controller
{
    public function __construct(
        private TagCreator $creator,
        private TagUpdater $updater,
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

    public function show(ShowTagRequest $request)
    {
        $detailTag = $this->detailTagRepository->findById($request->getTagId());

        return TagViewModel::fromDetailTag($detailTag);
    }

    public function update(TagUpdateRequest $request)
    {
        $tagData = TagData::fromArray($request->all());
        $tag = $request->getTag();

        $this->updater->update($tag, $tagData);

        $detailTag = $this->detailTagRepository->findById($tag->getId());
        return TagViewModel::fromDetailTag($detailTag);
    }

    public function store(TagCreateRequest $request)
    {
        $tagData = TagData::fromArray($request->all());
        $user = $request->getCurrentUser();

        $tag = $this->creator->create($user, $tagData);

        $detailTag = $this->detailTagRepository->findById($tag->getId());
        return TagViewModel::fromDetailTag($detailTag);
    }

    public function delete(TagDeleteRequest $request, TagRepository $tagRepository)
    {
        $tagRepository->remove($request->getTag());

        return response()->noContent();
    }
}
