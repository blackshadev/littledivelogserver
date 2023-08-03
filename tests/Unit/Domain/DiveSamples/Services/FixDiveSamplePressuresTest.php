<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\DiveSamples\Services;

use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\DiveSamples\DiveSamplesRepository;
use App\Domain\DiveSamples\Entities\DiveSamples;
use App\Domain\DiveSamples\Services\FixDiveSamplePressures;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;

final class FixDiveSamplePressuresTest extends MockeryTestCase
{
    private DiveSamplesRepository|MockInterface $diveSamples;

    private FixDiveSamplePressures $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->diveSamples = Mockery::mock(DiveSamplesRepository::class);
        $this->subject = new FixDiveSamplePressures($this->diveSamples);
    }

    public function testItReturnsUntouched(): void
    {
        $samples = new DiveSamples(DiveId::new(), []);

        $result = $this->subject->fix($samples);

        self::assertFalse($result->touched);
        self::assertSame($samples, $result->result);
    }

    public function testItUpdatedWhenTouched(): void
    {
        $input = new DiveSamples(DiveId::new(), [
            ['Time' => 0, 'Pressure' => [ [ 'Tank' => 0, 'Pressure' => 5 ],  [ 'Tank' => 0, 'Pressure' => 6 ]]]
        ]);

        $this->diveSamples
            ->expects('save')
            ->withArgs(fn (DiveSamples $samples) => $input->diveId() === $samples->diveId())
        ;

        $result = $this->subject->fix($input);

        self::assertTrue($result->touched);
        self::assertNotSame($input, $result->result);
    }
}
