<?php

declare(strict_types=1);

namespace App\Domain\Equipment\DataTransferObjects;

class TankPressureData
{
    private ?int $begin;

    private ?int $end;

    private string $type;

    public function __construct()
    {
        $this->type = 'bar';
        $this->begin = null;
        $this->end = null;
    }

    public static function fromArray(array $data = []): self
    {
        $pressures = new self();
        $pressures->begin = isset($data['begin']) ? (int)$data['begin'] : null;
        $pressures->end = isset($data['end']) ? (int)$data['end'] : null;
        $pressures->type = $data['type'] ?? 'bar';

        return $pressures;
    }

    public function getBegin(): ?int
    {
        return $this->begin;
    }

    public function getEnd(): ?int
    {
        return $this->end;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setBegin(?int $begin): void
    {
        $this->begin = $begin;
    }

    public function setEnd(?int $end): void
    {
        $this->end = $end;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
