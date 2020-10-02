<?php


namespace App\DataTransferObjects;


class PlaceData
{
    private ?int $id;
    private ?string $name;
    private ?string $countryCode;

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
}
