<?php

declare(strict_types=1);

namespace App\Http\Requests\Tags;

use App\Domain\Tags\Entities\Tag;
use App\Domain\Tags\Repositories\TagRepository;
use App\Domain\Users\Repositories\CurrentUserRepository;
use App\Error\TagNotFound;
use App\Http\Requests\AuthenticatedRequest;

class TagRequest extends AuthenticatedRequest
{
    public function __construct(
        private TagRepository $repository,
        CurrentUserRepository $currentUserRepository,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($currentUserRepository, $query, $request, $attributes, $cookies, $files, $server, $content);
    }

    public function getTagId(): int
    {
        $tagId = $this->route('tag');
        if (!filter_var($tagId, FILTER_VALIDATE_INT)) {
            throw new TagNotFound("Tag ${tagId} not found");
        }

        return (int)$tagId;
    }

    public function getTag(): Tag
    {
        return once(fn () => $this->repository->findById($this->getTagId()));
    }

    public function authorize()
    {
        return parent::authorize() && $this->getTag()->getUserId() === $this->getCurrentUser()->getId();
    }

    public function rules()
    {
        return [];
    }
}
