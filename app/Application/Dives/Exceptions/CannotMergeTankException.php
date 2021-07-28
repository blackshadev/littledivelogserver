<?php

declare(strict_types=1);

namespace App\Application\Dives\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CannotMergeTankException extends HttpException
{
    public function __construct(string $message)
    {
        parent::__construct(422, "Cannot merge tanks: " . $message);
    }

    public static function differentVolumes(): self
    {
        return new self("Tanks have different volume");
    }

    public static function differentPressureTypes(): self
    {
        return new self("Tanks have different pressure types");
    }

    public static function differentGasmixture(): self
    {
        return new self("Tanks have different gasmixure");
    }
}
