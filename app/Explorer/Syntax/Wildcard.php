<?php

declare(strict_types=1);

namespace App\Explorer\Syntax;

use JeroenG\Explorer\Domain\Syntax\SyntaxInterface;

class Wildcard implements SyntaxInterface
{
    public function __construct(
        private string $field,
        private string $value,
        private float $boost = 1.0
    ) {
    }

    public function build(): array
    {
        return [
            'wildcard' => [
                $this->field => [
                    'value' => $this->value,
                    'boost' => $this->boost
                ]
            ]
        ];
    }
}
