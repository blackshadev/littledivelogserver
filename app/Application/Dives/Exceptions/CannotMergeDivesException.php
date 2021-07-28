<?php

declare(strict_types=1);

namespace App\Application\Dives\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

final class CannotMergeDivesException extends HttpException
{
    private function __construct($message)
    {
        parent::__construct(422, "Cannot merge dives because: " . $message);
    }

    public static function computerDifference(): self
    {
        return new self("Dives are from different computers");
    }

    public static function placesDifference(): self
    {
        return new self("Dives are from different places");
    }

    public static function timeDifferenceToBig(): self
    {
        return new self("The time difference between dives to big");
    }

    public static function userDifference(): self
    {
        return new self("The dives don't belong to the same account");
    }

    public static function tooFewDives()
    {
        return new self("Too few dives. Requires at least two dives");
    }
}
