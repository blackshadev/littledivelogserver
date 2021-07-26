<?php

declare(strict_types=1);

namespace App\Domain\Dives\Entities;

use App\Domain\Dives\ValueObjects\GasMixture;
use App\Domain\Dives\ValueObjects\TankPressures;

final class DiveTank
{
    public function __construct(
        private ?int $id,
        private ?int $diveId,
        private ?int $volume,
        private GasMixture $gasMixture,
        private TankPressures $pressures
    ) {
    }

    public static function new(
        ?int $diveId,
        ?int $volume,
        GasMixture $gasMixture,
        TankPressures $pressures,
    ) {
        return new self(null, $diveId, $volume, $gasMixture, $pressures);
    }

    public static function existing(
        int $id,
        int $diveId,
        ?int $volume,
        GasMixture $gasMixture,
        TankPressures $pressures,
    ) {
        return new self($id, $diveId, $volume, $gasMixture, $pressures);
    }

    public function getDiveId(): ?int
    {
        return $this->diveId;
    }

    public function setDiveId(int $diveId): void
    {
        $this->diveId = $diveId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function setVolume(?int $volume): void
    {
        $this->volume = $volume;
    }

    public function getGasMixture(): GasMixture
    {
        return $this->gasMixture;
    }

    public function setGasMixture(GasMixture $gasMixture): void
    {
        $this->gasMixture = $gasMixture;
    }

    public function getPressures(): TankPressures
    {
        return $this->pressures;
    }

    public function setPressures(TankPressures $pressures): void
    {
        $this->pressures = $pressures;
    }

    public function isExisting(): bool
    {
        return $this->id !== null;
    }
}
