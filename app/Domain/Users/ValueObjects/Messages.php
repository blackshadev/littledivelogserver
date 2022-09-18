<?php

declare(strict_types=1);

namespace App\Domain\Users\ValueObjects;

enum Messages: string
{
    case AccountVerified = 'account.verified';
}
