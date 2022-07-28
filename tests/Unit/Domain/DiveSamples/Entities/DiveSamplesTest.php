<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\DiveSamples\Entities;

use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\DiveSamples\Entities\DiveSampleAccessor;
use App\Domain\DiveSamples\Entities\DiveSamples;
use App\Domain\DiveSamples\Visitors\DiveSampleVisitor;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class DiveSamplesTest extends MockeryTestCase
{
    public function testAcceptCallsVisitorPerSample(): void
    {
        $samples = [ [ 'Time' => 0 ], [ 'Time' => 1], ['Time' => 2]];
        $diveSamples = new DiveSamples(DiveId::new(), $samples);

        $subject = Mockery::mock(DiveSampleVisitor::class);
        $subject->expects('visit')
            ->withArgs(static fn (DiveSampleAccessor $arg) => in_array($arg->toArray(), $samples, true))
            ->times(3)
            ->andReturnUsing(static fn (DiveSampleAccessor $arg) => $arg->toArray());

        $newSamples = $diveSamples->accept($subject);

        self::assertNotSame($newSamples, $diveSamples);
        self::assertSame($newSamples->samples(), $diveSamples->samples());
    }

    public function testAcceptReturnsNewSample(): void
    {
        $expectedSamples = [ 'Time' => 1 ];
        $diveSamples = new DiveSamples(DiveId::new(), [ [ 'Time' => 0 ] ]);

        $subject = Mockery::mock(DiveSampleVisitor::class);
        $subject->expects('visit')
            ->andReturn($expectedSamples);

        $newSamples = $diveSamples->accept($subject);

        self::assertNotSame($newSamples, $diveSamples);
        self::assertSame([$expectedSamples], $newSamples->samples());
    }
}
