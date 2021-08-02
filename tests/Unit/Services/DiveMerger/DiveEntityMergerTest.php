<?php

declare(strict_types=1);

namespace Tests\Unit\Services\DiveMerger;

use App\Application\Dives\Services\Mergers\DiveEntityMergerImpl;
use App\Domain\Buddies\Entities\Buddy;
use App\Domain\Tags\Entities\Tag;
use PHPUnit\Framework\TestCase;

final class DiveEntityMergerTest extends TestCase
{
    /** @dataProvider entityDataProvider */
    public function testItMergesEntities(array $input, array $expected): void
    {
        $merger = new DiveEntityMergerImpl();

        $result = $merger->unique($input);

        self::assertEquals($expected, $result);
    }

    public function entityDataProvider()
    {
        yield 'empty' => [[], []];

        $buddy1 = Buddy::existing(1, 1, ':name1:', '#000000', null);
        $buddy2 = Buddy::existing(2, 1, ':name2:', '#000000', null);

        yield 'buddies' => [
            [$buddy2, $buddy2, $buddy1, $buddy1],
            [$buddy2, $buddy1]
        ];

        $tag1 = Tag::existing(1, 1, ':name1:', '#000000');
        $tag2 = Tag::existing(2, 1, ':name2:', '#000000');

        yield 'tags' => [
            [$tag1, $tag2, $tag1, $tag2],
            [$tag1, $tag2]
        ];
    }
}
