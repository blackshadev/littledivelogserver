<?php

declare(strict_types=1);

namespace App\Http\Requests\Computers;

use App\Domain\Computers\Entities\Computer;
use App\Domain\Computers\Repositories\ComputerRepository;
use App\Domain\Users\Repositories\CurrentUserRepository;
use App\Error\ComputerNotFound;
use App\Http\Requests\AuthenticatedRequest;

abstract class ComputerRequest extends AuthenticatedRequest
{
    public function __construct(
        private ComputerRepository $repository,
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

    public function getComputerId(): int
    {
        $computerId = $this->route('computer');
        if (!filter_var($computerId, FILTER_VALIDATE_INT)) {
            throw new ComputerNotFound("Computer ${computerId} not found");
        }

        return (int)$computerId;
    }

    public function getComputer(): Computer
    {
        return once(fn () => $this->repository->findById($this->getComputerId()));
    }

    public function authorize()
    {
        return parent::authorize() && $this->getComputer()->getUserId() === $this->getCurrentUser()->getId();
    }

    public function rules()
    {
        return [];
    }
}
