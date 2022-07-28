<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\DiveSamples\Visitors;

use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\DiveSamples\Entities\DiveSamples;
use App\Domain\DiveSamples\Visitors\DivePressureUniqueifier;
use PHPUnit\Framework\TestCase;

final class DivePressureUniqueifierTest extends TestCase
{
    /** @dataProvider provideSkippableData */
    public function testItSkipsWhenNothingToDo(array $data): void
    {
        $visitor = new DivePressureUniqueifier();
        $diveSamples = DiveSamples::create(DiveId::new(), $data);

        $newDiveSamples = $diveSamples->accept($visitor);

        self::assertFalse($visitor->hasUpdatedSamples());
        self::assertEquals($diveSamples, $newDiveSamples);
    }

    public static function provideSkippableData(): iterable
    {
        yield 'Empty' => [[ ]];
        yield 'No pressure' => [[[ 'Time' => 0, ]]];
        yield 'Single Pressure' => [[[ 'Time' => 0, 'Pressure' => [['Tank' => 0, 'Pressure' => 142 ]]]]];
        yield 'Multiple Different Pressures' => [[[ 'Time' => 0, 'Pressure' => [['Tank' => 0, 'Pressure' => 142 ], ['Tank' => 2, 'Pressure' => 55 ]]]]];
    }

    /** @dataProvider provideInvalidData */
    public function testItFailsOnInvalidData(array $data): void
    {
        $visitor = new DivePressureUniqueifier();
        $diveSamples = DiveSamples::create(DiveId::new(), $data);

        self::expectException(\InvalidArgumentException::class);

        $diveSamples->accept($visitor);
    }

    public function provideInvalidData(): iterable
    {
        yield 'Missing Tank' => [[[ 'Time' => 0, 'Pressure' => [['Pressure' => 142]]]]];
        yield 'Missing Pressure' => [[[  'Time' => 0, 'Pressure' => [['Tank' => 0]]]]];
    }

    /** @dataProvider provideUnifiableData */
    public function testItUnifiesPressures(array $data, array $expected): void
    {
        $visitor = new DivePressureUniqueifier();
        $diveSamples = DiveSamples::create(DiveId::new(), $data);

        $newSamples = $diveSamples->accept($visitor);

        self::assertTrue($visitor->hasUpdatedSamples());
        self::assertEquals($expected, $newSamples->samples());
    }

    public static function provideUnifiableData(): iterable
    {
        yield 'Single sample; multiple pressures' => [
            [['Time' => 0, 'Pressure' => [ [ 'Tank' => 0, 'Pressure' => 150 ], [ 'Tank' => 0, 'Pressure' => 142 ] ]]],
            [['Time' => 0, 'Pressure' => [ [ 'Tank' => 0, 'Pressure' => 142 ] ]]]
        ];

        yield 'Multiple sample; multiple pressures' => [
            [
                ['Time' => 0, 'Pressure' => [ [ 'Tank' => 0, 'Pressure' => 150 ], [ 'Tank' => 0, 'Pressure' => 142 ] ]],
                ['Time' => 2, 'Pressure' => [ [ 'Tank' => 1, 'Pressure' => 72 ], [ 'Tank' => 1, 'Pressure' => 75 ] ]],
            ],
            [
                ['Time' => 0, 'Pressure' => [ [ 'Tank' => 0, 'Pressure' => 142 ] ]],
                ['Time' => 2, 'Pressure' => [ [ 'Tank' => 1, 'Pressure' => 75 ] ]],
            ],
        ];
    }
}
