<?php

declare(strict_types=1);

namespace App\Http\Requests\Dives;

use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Users\Repositories\CurrentUserRepository;
use App\Http\Requests\AuthenticatedRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class DiveRequest extends AuthenticatedRequest
{
    public function __construct(
        private DiveRepository $diveRepository,
        CurrentUserRepository $currentUserRepository,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct(
            $currentUserRepository,
            $query,
            $request,
            $attributes,
            $cookies,
            $files,
            $server,
            $content,
        );
    }

    public function getDive(): Dive
    {
        return once(fn () => $this->diveRepository->findById($this->getDiveId()));
    }

    public function getDiveId(): int
    {
        $routeParam = $this->route('dive');
        if (!filter_var($routeParam, FILTER_VALIDATE_INT)) {
            throw new NotFoundHttpException("Dive ${routeParam} not found");
        }

        return (int)$routeParam;
    }

    public function authorize()
    {
        return parent::authorize() && $this->getDive()->getUserId() === $this->getCurrentUser()->getId();
    }
}
