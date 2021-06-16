<?php

declare(strict_types=1);

namespace App\Domain\Computers\DataTransferObjects;

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

    public function setSerial(int $serial): void
    {
        $this->serial = $serial;
    }

    public function setVendor(string $vendor): void
    {
        $this->vendor = $vendor;
    }

    public function setModel(int $model): void
    {
        $this->model = $model;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
