<?php

declare(strict_types=1);

namespace App\Domain;

interface EntityWithId
{
    public function getId(): int;
}
