<?php

declare(strict_types=1);

namespace App\Domain\Equipment\Entities;

use Webmozart\Assert\Assert;

final class Tank
{
    public const PRESSURE_TYPE_BAR = 'bar';

    public const PRESSURE_TYPE_PSI = 'psi';

    private function __construct(
        private ?int $id,
        private int $volume,
        private int $oxygen,
        private int $beginPressure,
        private int $endPressure,
        private string $pressureType,
    ) {
        $this->validatePressures($beginPressure, $this->endPressure);
        $this->validatePressureType($pressureType);
    }

    public static function new(
        int $volume,
        int $oxygen,
        int $beginPressure,
        int $endPressure,
        string $pressureType,
    ): self {
        return new self(
            id: null,
            volume: $volume,
            oxygen: $oxygen,
            beginPressure: $beginPressure,
            endPressure: $endPressure,
            pressureType: $pressureType,
        );
    }

    public static function existing(
        int $id,
        int $volume,
        int $oxygen,
        int $beginPressure,
        int $endPressure,
        string $pressureType,
    ): self {
        return new self(
            id: $id,
            volume: $volume,
            oxygen: $oxygen,
            beginPressure: $beginPressure,
            endPressure: $endPressure,
            pressureType: $pressureType,
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getVolume(): int
    {
        return $this->volume;
    }

    public function setVolume(int $volume): void
    {
        $this->volume = $volume;
    }

    public function getOxygen(): int
    {
        return $this->oxygen;
    }

    public function setOxygen(int $oxygen): void
    {
        $this->oxygen = $oxygen;
    }

    public function getBeginPressure(): int
    {
        return $this->beginPressure;
    }

    public function setBeginPressure(int $beginPressure): void
    {
        $this->validatePressures($beginPressure, $this->endPressure);
        $this->beginPressure = $beginPressure;
    }

    public function getEndPressure(): int
    {
        return $this->endPressure;
    }

    public function setEndPressure(int $endPressure): void
    {
        $this->validatePressures($this->beginPressure, $endPressure);
        $this->endPressure = $endPressure;
    }

    public function getPressureType(): string
    {
        return $this->pressureType;
    }

    public function setPressureType(string $pressureType): void
    {
        $this->validatePressureType($pressureType);
        $this->pressureType = $pressureType;
    }

    private function validatePressureType(string $pressureType): void
    {
        Assert::oneOf($pressureType, [self::PRESSURE_TYPE_BAR, self::PRESSURE_TYPE_PSI]);
    }

    private function validatePressures(int $beginPressure, int $endPressure): void
    {
        Assert::greaterThanEq($beginPressure, $endPressure);
    }
}
