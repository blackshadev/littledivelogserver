<?php

namespace App\DataTransferObjects;

class ComputerData
{
    private int $serial;
    private string $vendor;
    private int $model;
    private int $type;
    private string $name;

    public static function fromArray(array $data): self
    {
        $computer = new self();
        $computer->serial = $data['serial'];
        $computer->vendor = $data['vendor'];
        $computer->model = $data['model'];
        $computer->type = $data['type'];
        $computer->name = $data['name'];

        return $computer;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getModel(): int
    {
        return $this->model;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSerial(): int
    {
        return $this->serial;
    }
}
