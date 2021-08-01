<?php

declare(strict_types=1);

namespace App\Http\Requests\Equipment;

use App\Domain\Equipment\Entities\Equipment;
use App\Domain\Equipment\Repositories\EquipmentRepository;
use App\Domain\Users\Repositories\CurrentUserRepository;
use App\Http\Requests\AuthenticatedRequest;

final class EquipmentRequest extends AuthenticatedRequest
{
    public function __construct(
        private EquipmentRepository $equipmentRepository,
        CurrentUserRepository $currentUserRepository,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null,
    ) {
        parent::__construct($currentUserRepository, $query, $request, $attributes, $cookies, $files, $server, $content);
    }

    public function getEquipment(): Equipment
    {
        return $this->equipmentRepository->forUser($this->getCurrentUser()->getId());
    }

    public function rules()
    {
        return [];
    }
}
