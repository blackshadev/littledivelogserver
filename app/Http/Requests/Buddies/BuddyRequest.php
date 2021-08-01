<?php

declare(strict_types=1);

namespace App\Http\Requests\Buddies;

use App\Domain\Buddies\Entities\Buddy;
use App\Domain\Buddies\Repositories\BuddyRepository;
use App\Domain\Users\Repositories\CurrentUserRepository;
use App\Error\BuddyNotFound;
use App\Http\Requests\AuthenticatedRequest;

final class BuddyRequest extends AuthenticatedRequest
{
    public function __construct(
        private BuddyRepository $repository,
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

    public function getBuddyId(): int
    {
        $buddyId = $this->route('buddy');
        if (!filter_var($buddyId, FILTER_VALIDATE_INT)) {
            throw new BuddyNotFound("Buddy ${buddyId} not found");
        }

        return (int)$buddyId;
    }

    public function getBuddy(): Buddy
    {
        return once(fn () => $this->repository->findById($this->getBuddyId()));
    }

    public function authorize()
    {
        return parent::authorize() && $this->getBuddy()->getUserId() === $this->getCurrentUser()->getId();
    }

    public function rules()
    {
        return [];
    }
}
