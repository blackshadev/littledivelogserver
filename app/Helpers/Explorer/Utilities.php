<?php

declare(strict_types=1);

namespace App\Helpers\Explorer;

use App\Helpers\Arrg;
use JeroenG\Explorer\Application\BuildCommand;

class Utilities
{
    public static function buildCommandToArray(BuildCommand $cmd): array
    {
        return [
            'must' => self::toArray($cmd->getMust()),
            'should' => self::toArray($cmd->getShould()),
            'filter' => self::toArray($cmd->getFilter()),
        ];
    }

    public static function toArray(array $array): array
    {
        return Arrg::map($array, fn ($i) => $i->build());
    }
}
