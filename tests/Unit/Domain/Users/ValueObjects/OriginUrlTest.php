<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Users\ValueObjects;

use App\Domain\Users\ValueObjects\OriginUrl;
use PHPUnit\Framework\TestCase;

final class OriginUrlTest extends TestCase
{
    /** @dataProvider invalidURLs */
    public function testItValidatesInput(string $invalidInput): void
    {
        $this->expectException(\InvalidArgumentException::class);
        OriginUrl::fromString($invalidInput);
    }

    public function testItCreatesFromString(): void
    {
        $input = 'https://example.com';

        $subject = OriginUrl::fromString($input);

        self::assertSame($input, $subject->toString());
    }

    public function testItAddsMessage(): void
    {
        $input = 'https://example.com';

        $subject = OriginUrl::fromString($input)->withMessage(':msg:');

        self::assertStringContainsString($input, $subject->toString());
        self::assertStringContainsString(':msg:', $subject->toString());
    }

    public function invalidURLs()
    {
        yield 'empty' => [ '' ];
        yield 'random string' => [ 'random' ];
        yield 'with query string' => [ 'http://example.com?test=yes' ];
    }
}
