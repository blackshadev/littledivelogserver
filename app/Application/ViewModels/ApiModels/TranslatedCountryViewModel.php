<?php

declare(strict_types=1);

namespace App\Application\ViewModels\ApiModels;

use App\Application\ViewModels\ViewModel;
use App\Domain\Countries\Entity\Country;

final class TranslatedCountryViewModel extends ViewModel
{
    protected array $visible = ['name', 'iso2'];

    public function __construct(
        private string $iso2,
        private string $name,
    ) {
    }

    public static function fromCountry(Country $country): self
    {
        return new self($country->getIso2(), $country->getName());
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIso2()
    {
        return $this->iso2;
    }
}
