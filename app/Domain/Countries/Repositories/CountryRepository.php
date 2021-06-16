<?php

declare(strict_types=1);

namespace App\Domain\Countries\Repositories;

use App\Domain\Countries\Entity\Country;

interface CountryRepository
{
    /** @return Country[] */
    public function list(): array;
}
