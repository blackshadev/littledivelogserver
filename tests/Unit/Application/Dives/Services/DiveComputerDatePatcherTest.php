<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Dives\Services;

use App\Application\Dives\DataTransferObjects\DiveData;
use App\Application\Dives\Services\DiveComputerDataPatcher;
use App\Application\Dives\Services\DiveCreatorInterface;
use App\Domain\Buddies\Entities\Buddy;
use App\Domain\Computers\Entities\Computer;
use App\Domain\Computers\Repositories\ComputerRepository;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Dives\Entities\DiveTank;
use App\Domain\Dives\Repositories\DiveRepository;
use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\Dives\ValueObjects\GasMixture;
use App\Domain\Dives\ValueObjects\TankPressures;
use App\Domain\Places\Entities\Place;
use App\Domain\Tags\Entities\Tag;
use App\Domain\Users\Entities\User;
use Carbon\CarbonImmutable;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;

final class DiveComputerDatePatcherTest extends MockeryTestCase
{
    private DiveRepository|MockInterface $diveRepository;

    private ComputerRepository|MockInterface $computerRepository;

    private DiveCreatorInterface|MockInterface $diveCreator;

    private DiveComputerDataPatcher $subject;

    protected function setUp(): void
    {
        $this->diveRepository = Mockery::mock(DiveRepository::class);
        $this->computerRepository = Mockery::mock(ComputerRepository::class);
        $this->diveCreator = Mockery::mock(DiveCreatorInterface::class);

        $this->subject = new DiveComputerDataPatcher(
            $this->diveRepository,
            $this->computerRepository,
            $this->diveCreator,
        );
    }

    public function testItCreates(): void
    {
        $dive = self::createDive();
        $user = User::fromArray([
            'id' => -2,
            'name' => 'test',
            'email' => 'test@test.nl',
            'origin' => 'https://test.nl'
        ]);

        $newComputer = Computer::existing(-2, -3, 0, ':vendor:', 0, 0, ':computer-2:');
        $data = DiveData::fromArray([
            'fingerprint' => 'bbb',
            'date' => CarbonImmutable::parse('2023-01-03'),
            'divetime' => 1500,
            'computer_id' => $newComputer->getId(),
            'max_depth' => 16.3,
            'tanks' => [
                [ 'volume' => 12, 'oxygen' => 32, 'pressure' => ['begin' => 200, 'end' => 50, 'type' => 'bar' ] ],
            ],
            'samples' => [
                [ 'sample' => 2 ],
            ]
        ]);

        $this->diveRepository
            ->expects('findByFingerprint')
            ->with(-2, -3, 'bbb')
            ->andReturn(null);

        $this->diveCreator->expects('create')->with($user, $data)->andReturn($dive->getDiveId());

        $result = $this->subject->patchOrCreate($user, $data);

        self::assertSame($result, $dive->getDiveId());
    }

    public function testItPatchesData(): void
    {
        $dive = self::createDive();
        $user = User::fromArray([
            'id' => -2,
            'name' => 'test',
            'email' => 'test@test.nl',
            'origin' => 'https://test.nl'
        ]);

        $newComputer = Computer::existing(-2, -3, 0, ':vendor:', 0, 0, ':computer-2:');
        $data = DiveData::fromArray([
            'fingerprint' => 'bbb',
            'date' => CarbonImmutable::parse('2023-01-03'),
            'divetime' => 1500,
            'computer_id' => $newComputer->getId(),
            'max_depth' => 16.3,
            'tanks' => [
                [ 'volume' => 12, 'oxygen' => 32, 'pressure' => ['begin' => 200, 'end' => 50, 'type' => 'bar' ] ],
            ],
            'samples' => [
                [ 'sample' => 2 ],
            ]
        ]);

        $this->computerRepository->expects('findById')->with(-3)->andReturn($newComputer);
        $this->diveRepository
            ->expects('findByFingerprint')
            ->with(-2, -3, 'bbb')
            ->andReturn($dive);

        $this->diveRepository->expects('save')->with($dive)->andReturn($dive->getDiveId());

        $result = $this->subject->patchOrCreate($user, $data);

        self::assertSame($result, $dive->getDiveId());
        self::assertSame($newComputer, $dive->getComputer());
        self::assertSame($data->maxDepth, $dive->getMaxDepth());
        self::assertSame($data->divetime, $dive->getDivetime());
        self::assertSame($data->date, $dive->getDate());
        self::assertSame($data->fingerprint, $dive->getFingerprint());
        self::assertSame($data->tanks[0]->getVolume(), $dive->getTanks()[0]->getVolume());
        self::assertSame($data->tanks[0]->getOxygen(), $dive->getTanks()[0]->getGasMixture()->getOxygen());
        self::assertEquals($data->tanks[0]->getPressures()->getBegin(), $dive->getTanks()[0]->getPressures()->getBegin());
        self::assertEquals($data->tanks[0]->getPressures()->getEnd(), $dive->getTanks()[0]->getPressures()->getEnd());
        self::assertEquals($data->tanks[0]->getPressures()->getType(), $dive->getTanks()[0]->getPressures()->getType());
        self::assertEquals($data->samples, $dive->getSamples());

        self::assertSame(-10, $dive->getPlace()->getId());
        self::assertSame(-30, $dive->getTags()[0]->getId());
        self::assertSame(-40, $dive->getBuddies()[0]->getId());
    }

    private static function createDive(): Dive
    {
        return Dive::existing(
            DiveId::existing(-1),
            -2,
            CarbonImmutable::parse('2023-01-01'),
            CarbonImmutable::parse('2023-01-02'),
            1400,
            15.2,
            Computer::existing(-2, -5, 0, ':vendor:', 0, 0, ':computer:'),
            'aaaa',
            Place::existing(-10, -2, ':ctry:', 'NL'),
            [
                DiveTank::existing(-20, 10, 21, new GasMixture(21), new TankPressures('bar', 200, 50)),
            ],
            [
                Tag::existing(-30, -2, ':buddy:', '#fff'),
            ],
            [
                Buddy::existing(-40, -2, ':buddy:', '#fff', null),
            ],
            [ [ 'sample' => 1 ]  ],
        );
    }
}
