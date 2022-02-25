<?php

declare(strict_types=1);

namespace App\Http\Requests\Dives;

use App\Domain\Dives\Entities\DiveSamples;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Dives\Repositories\DiveSamplesRepository;
use App\Domain\Users\Repositories\CurrentUserRepository;

final class DiveSamplesRequest extends DiveRequest
{
    public function __construct(
        private DiveSamplesRepository $diveSamplesRepository,
        DiveRepository $diveRepository,
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
            $diveRepository,
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

    public function getDiveSamples(): DiveSamples
    {
        return once(fn () => $this->diveSamplesRepository->findById($this->getDiveId()));
    }
}
