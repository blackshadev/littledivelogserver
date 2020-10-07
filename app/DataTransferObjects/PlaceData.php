<?php


namespace App\DataTransferObjects;


class PlaceData
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $countryCode = null;

    public static function fromArray(array $data): self
    {
        $place = new PlaceData();
        $place->id = $data['place_id'] ?? null;
        $place->name = $data['name'] ?? null;
        $place->countryCode = $data['country_code'] ?? null;
        return $place;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function isEmpty(): bool
    {
        return $this->id === null && $this->name === null;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }
}
