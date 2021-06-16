<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Computers\Entities\DetailComputer;
use DateTimeInterface;

class ComputerListViewModel extends ViewModel
{
    protected array $visible = [
        'computer_id', 'name', 'serial', 'vendor', 'model', 'type', 'dive_count', 'last_read', 'last_fingerprint',
    ];

    public function __construct(
        private int $computerId,
        private int $serial,
        private string $vendor,
        private int $model,
        private int $type,
        private string $name,
        private int $diveCount,
        private ?DateTimeInterface $lastRead,
        private ?string $lastFingerprint,
    ) {
    }

    public static function fromDetailModel(DetailComputer $detailComputer): self
    {
        return new self(
            computerId: $detailComputer->getComputerId(),
            model: $detailComputer->getModel(),
            serial: $detailComputer->getSerial(),
            type: $detailComputer->getType(),
            vendor: $detailComputer->getVendor(),
            name: $detailComputer->getName(),
            diveCount: $detailComputer->getDiveCounts(),
            lastRead: $detailComputer->getLastRead(),
            lastFingerprint: $detailComputer->getLastFingerprint(),
        );
    }

    public function getComputerId()
    {
        return $this->computerId;
    }

    public function getSerial()
    {
        return $this->serial;
    }

    public function getVendor()
    {
        return $this->vendor;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDiveCount()
    {
        return $this->diveCount;
    }

    public function getLastRead()
    {
        return $this->lastRead;
    }

    public function getLastFingerprint()
    {
        return $this->lastFingerprint;
    }
}
