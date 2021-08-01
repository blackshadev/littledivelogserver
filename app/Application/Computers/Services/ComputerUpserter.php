<?php

declare(strict_types=1);

namespace App\Application\Computers\Services;

use App\Application\Computers\DataTransferObjects\ComputerData;
use App\Domain\Computers\Entities\Computer;
use App\Domain\Computers\Repositories\ComputerRepository;
use App\Domain\Users\Entities\User;

final class ComputerUpserter
{
    public function __construct(
        private ComputerRepository $computerRepository
    ) {
    }

    public function create(User $user, ComputerData $data): Computer
    {
        $computer = $this->computerRepository->findBySerial($user, $data->getSerial());

        if (!is_null($computer)) {
            $computer->setName($data->getName());
            $computer->setModel($data->getModel());
            $computer->setType($data->getType());
            $computer->setVendor($data->getVendor());
        } else {
            $computer = Computer::new(
                userId: $user->getId(),
                name: $data->getName(),
                vendor: $data->getVendor(),
                model: $data->getModel(),
                serial: $data->getSerial(),
                type: $data->getType(),
            );
        }
        $this->computerRepository->save($computer);

        return $computer;
    }
}
