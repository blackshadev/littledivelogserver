<?php

declare(strict_types=1);

namespace App\Error;

use RuntimeException;

final class SaveOperationFailed extends RuntimeException
{
    public static function singleRow(int $affectedRows): self
    {
        return new self(
            sprintf('Save operation failed, expected one record to be updated, got %d', $affectedRows)
        );
    }
}
