<?php

namespace App\DataTransferObjects;

class TankPressureData
{
    private ?int $begin;
    private ?int $end;
    private string $type;

    public static function fromArray(array $data): self
    {
        $pressures = new TankPressureData();
        $pressures->begin = $data['begin'] ?? null;
        $pressures->end = $data['end'] ?? null;
        $pressures->type = $data['type'] ?? 'bar';
        return $pressures;
    }

    public function __construct()
    {
        $this->type = 'bar';
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
