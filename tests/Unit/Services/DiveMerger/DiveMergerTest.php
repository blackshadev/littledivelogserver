<?php

declare(strict_types=1);

/**
 * TODO: This is not a unit test because it uses the database.
 * In order to make this a unit test we need to NOT use eloquent entities throughout the
 * Services, this will be a major rework and is postponed to later
 */

namespace Tests\Unit\Services\DiveMerger;

use App\Application\Dives\Exceptions\CannotMergeDivesException;
use App\Application\Dives\Services\Mergers\DiveEntityMerger;
use App\Application\Dives\Services\Mergers\DiveEntityMergerImpl;
use App\Application\Dives\Services\Mergers\DiveMergerImpl;
use App\Application\Dives\Services\Mergers\DiveSampleCombiner;
use App\Application\Dives\Services\Mergers\DiveSampleCombinerImpl;
use App\Application\Dives\Services\Mergers\DiveTankMerger;
use App\Application\Dives\Services\Mergers\DiveTankMergerImpl;
use App\Domain\Computers\Entities\Computer;
use App\Domain\Dives\Entities\Dive;
use App\Domain\Places\Entities\Place;
use DateTimeImmutable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;

final class DiveMergerTest extends MockeryTestCase
{
    use WithFaker;
    use DatabaseTransactions;

    public const USERID = 1;

    public const DATETIME = '2020-10-09 10:10:10';

    private DiveMergerImpl $subject;

    private DiveTankMergerImpl |

MockInterface $diveTankMerger;

    private DiveEntityMergerImpl |

MockInterface $entityMerger;

    private DiveSampleCombinerImpl |

MockInterface $diveSampleCombiner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityMerger = Mockery::mock(DiveEntityMerger::class);
        $this->diveTankMerger = Mockery::mock(DiveTankMerger::class);
        $this->diveSampleCombiner = Mockery::mock(DiveSampleCombiner::class);

        $this->subject = new DiveMergerImpl(
            $this->diveTankMerger,
            $this->entityMerger,
            $this->diveSampleCombiner,
        );
    }

    #[DataProvider('basicAttributeDivesDataProvider')]
    public function testItMergesBasicDiveAttributes(array $dives, Dive $target): void
    {
        $this->diveSampleCombiner->expects('combine')->andReturn([]);
        $this->entityMerger->expects('unique')->twice()->with([])->andReturn([]);
        $this->diveTankMerger->expects('mergeForDives')->andReturn([]);

        $newDive = $this->subject->merge($dives);

        self::assertEquals($target->getDate(), $newDive->getDate());
        self::assertEquals($target->getMaxDepth(), $newDive->getMaxDepth());
        self::assertEquals($target->getDivetime(), $newDive->getDivetime());
        self::assertEquals($target->getUserId(), $newDive->getUserId());
        self::assertEquals($target->getPlace(), $newDive->getPlace());
        self::assertEquals($target->getComputer(), $newDive->getComputer());
    }

    public static function basicAttributeDivesDataProvider()
    {
        $computer = Computer::new(self::USERID, 0, ':vendor:', 0, 0, ':name:');

        yield 'selects min date' => [
            [
                Dive::new(self::USERID, new DateTimeImmutable('2020-10-09 10:10:10')),
                Dive::new(self::USERID, new DateTimeImmutable('2020-10-09 10:40:10')),
                Dive::new(self::USERID, new DateTimeImmutable('2020-10-09 09:10:10')),
            ],
            Dive::new(self::USERID, new DateTimeImmutable('2020-10-09 09:10:10'))
        ];

        yield 'selects max depth' => [
            [
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), maxDepth: 1.0),
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), maxDepth: 10.0),
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), maxDepth: 2.0),
            ],
            Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), maxDepth: 10.0)
        ];

        yield 'summarizes divetime' => [
            [
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), divetime: 10),
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), divetime: 60),
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), divetime: 140),
            ],
            Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), divetime: 210)
        ];

        $place = Place::existing(1, self::USERID, ':test1:', 'NL');
        yield 'selects first place' => [
            [
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), place: null),
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), place: $place),
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), place: null),
            ],
            Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), place: $place),
        ];

        yield 'Prefers divecomputer on divetime' => [
            [
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), divetime: 10, computer: $computer),
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), divetime: 60),
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), divetime: 140, computer: $computer),
            ],
            Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), divetime: 150, computer: $computer),
        ];

        yield 'Prefers divecomputer on date' => [
            [
                Dive::new(self::USERID, new DateTimeImmutable('2020-10-09 09:10:10')),
                Dive::new(self::USERID, new DateTimeImmutable('2020-10-09 10:10:10'), computer: $computer),
                Dive::new(self::USERID, new DateTimeImmutable('2020-10-09 10:40:10'), computer: $computer),
            ],
            Dive::new(self::USERID, new DateTimeImmutable('2020-10-09 10:10:10'), computer: $computer),
        ];
    }

    #[DataProvider('invalidDiveMergeDataProvider')]
    public function testItThrowsExceptions(array $dives): void
    {
        $this->diveSampleCombiner->expects('combine')->never();
        $this->entityMerger->expects('unique')->never();
        $this->diveTankMerger->expects('mergeForDives')->never();

        $this->expectException(CannotMergeDivesException::class);

        $this->subject->merge($dives);
    }

    public static function invalidDiveMergeDataProvider()
    {
        yield 'No dives' => [
            [],
        ];

        yield 'One dive' => [
            [
                Dive::new(self::USERID, new DateTimeImmutable('2020-10-10 10:10:10')),
            ],
        ];

        yield 'Dates to much seperated' => [
            [
                Dive::new(self::USERID, new DateTimeImmutable('2020-10-10 10:10:10')),
                Dive::new(self::USERID, new DateTimeImmutable('2020-10-10 12:11:10')),
            ],
        ];

        yield 'Different places' => [
            [
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), place: Place::existing(1, 1, ':test1:', 'NL')),
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), place: Place::existing(5, 1, ':test1:', 'NL')),
            ],
        ];

        yield 'Different Computers' => [
            [
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), computer: Computer::existing(self::USERID, 1, 0, '', 0, 0, '')),
                Dive::new(self::USERID, new DateTimeImmutable(self::DATETIME), computer: Computer::existing(self::USERID, 5, 0, '', 0, 0, '')),
            ],
        ];

        yield 'Different Users' => [
            [
                Dive::new(1, new DateTimeImmutable(self::DATETIME)),
                Dive::new(5, new DateTimeImmutable(self::DATETIME)),
            ],
        ];
    }
}
