<?php

declare(strict_types=1);

/**
 * TODO: This is not a unit test because it uses the database.
 * In order to make this a unit test we need to NOT use eloquent entities throughout the
 * Services, this will be a major rework and is postponed to later
 */

namespace Tests\Unit\Services\DiveMerger;

use App\Domain\Support\Arrg;
use App\Models\Buddy;
use App\Models\Dive;
use App\Models\DiveTank;
use App\Models\Tag;
use App\Models\User;
use App\Services\DiveMerger\CannotMergeDivesException;
use App\Services\DiveMerger\DiveMergerService;
use App\Services\Repositories\DiveRepository;
use Carbon\Carbon;
use Database\Factories\DiveFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class DiveMergerTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /** @var DiveRepository|Mockery\LegacyMockInterface|Mockery\MockInterface  */
    private DiveRepository $diveRepository;

    private DiveMergerService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->diveRepository = Mockery::mock(DiveRepository::class);
        $this->subject = new DiveMergerService($this->diveRepository);
    }

    public function testItMergesDivesBasicData()
    {
        $dives = $this->makeDives([
            [
                'date' => new Carbon('2020-10-10 12:00:00'),
                'max_depth' => '5.5'
            ],
            [
                'date' => new Carbon('2020-10-10 11:00:00'),
                'max_depth' => '1.0'
            ],
            [
                'date' => new Carbon('2020-10-10 10:00:00'),
                'max_depth' => '3.2'
            ]
        ]);

        $newDive = $this->subject->mergeDives($dives);

        self::assertEquals(new Carbon('2020-10-10 10:00:00'), $newDive->getDate());
        self::assertEquals(5.5, $newDive->getMaxDepth());
    }

    public function testItMergesDivesBuddies()
    {
        [$bud1, $bud2, $bud3] = Buddy::factory()->count(4)->create();
        /** @var Dive $dive1 */

        [$dive1, $dive2] = $this->makeDives(
            2,
        );

        $dive1->buddies()->saveMany([$bud1, $bud2]);
        $dive2->buddies()->saveMany([$bud3, $bud2]);

        $newDive = $this->subject->mergeDives([$dive1, $dive2]);

        $buddies = $newDive->getBuddies();
        self::assertCount(3, $buddies);
        self::assertEquals(
            array_values(Arrg::map($buddies, fn ($item) => $item->getId())),
            Arrg::get([$bud1, $bud2, $bud3], "id")
        );
    }

    public function testItMergesDivesTags()
    {
        [$bud1, $tag2, $tag3] = Tag::factory()->count(4)->create();
        /** @var Dive $dive1 */
        /** @var Dive $dive2 */
        [$dive1, $dive2] = $this->makeDives(
            2,
        );

        $dive1->tags()->saveMany([$bud1, $tag2]);
        $dive2->tags()->saveMany([$tag3, $tag2]);

        $newDive = $this->subject->mergeDives([$dive1, $dive2]);

        $buddies = $newDive->getTags();
        self::assertCount(3, $buddies);
        self::assertEquals(
            array_values(Arrg::map($buddies, fn ($item) => $item->getId())),
            Arrg::get([$bud1, $tag2, $tag3], "id")
        );
    }

    public function testItMergesDivesTanks()
    {
        $tank1 = DiveTank::factory()->createOne([
            'pressure_begin' => 200,
            'pressure_end' => 100,
            'volume' => 11,
            'oxygen' => 21,
            'pressure_type' => 'bar'
        ]);
        $tank2 = DiveTank::factory()->createOne([
            'pressure_begin' => 98,
            'pressure_end' => 50,
            'volume' => 11,
            'oxygen' => 21,
            'pressure_type' => 'bar'
        ]);

        /** @var Dive $dive1 */
        /** @var Dive $dive2 */
        [$dive1, $dive2] = $this->makeDives(
            2,
        );
        $dive1->tanks()->save($tank1);
        $dive2->tanks()->save($tank2);

        $newDive = $this->subject->mergeDives([$dive1, $dive2]);

        $tanks = $newDive->getTanks();
        self::assertCount(1, $tanks);
        self::assertEquals(200, $tanks[0]->getPressures()->getBegin());
        self::assertEquals(50, $tanks[0]->getPressures()->getEnd());
    }

    public function testItThrowsOnDifferentPlaces()
    {
        [$dive1, $dive2] = $this->makeDives(
            2,
        );
        $dive1->place_id = 9;
        $dive2->place_id = 10;

        $this->expectException(CannotMergeDivesException::class);

        $this->subject->mergeDives([$dive1, $dive2]);
    }

    public function testItThrowsOnDifferentDates()
    {
        [$dive1, $dive2] = $this->makeDives(
            2,
        );
        $dive1->date = new Carbon('2019-12-12 12:12:00');
        $dive2->date = new Carbon('2019-12-16 12:12:00');

        $this->expectException(CannotMergeDivesException::class);

        $this->subject->mergeDives([$dive1, $dive2]);
    }

    public function testItThrowsOnDifferentUsers()
    {
        [$dive1, $dive2] = $this->makeDives(
            2,
        );
        $dive1->user_id = 2;
        $dive2->user_id = 5;

        $this->expectException(CannotMergeDivesException::class);

        $this->subject->mergeDives([$dive1, $dive2]);
    }

    public function testItThrowsOnDifferentComputers()
    {
        [$dive1, $dive2] = $this->makeDives(
            2,
        );
        $dive1->computer_id = 2;
        $dive2->computer_id = 5;

        $this->expectException(CannotMergeDivesException::class);

        $this->subject->mergeDives([$dive1, $dive2]);
    }

    public function testItThrowsOnTooFewDives()
    {
        $dive1 = new Dive();

        $this->expectException(CannotMergeDivesException::class);

        $this->subject->mergeDives([$dive1]);
    }

    private function makeDives($attributeSetsOrNumOrFactory, ?DiveFactory $factory = null)
    {
        if (is_array($attributeSetsOrNumOrFactory)) {
            $attributeSets = $attributeSetsOrNumOrFactory;
        } elseif (is_int($attributeSetsOrNumOrFactory)) {
            $attributeSets = [];
            for ($iX = 0; $iX < $attributeSetsOrNumOrFactory; $iX++) {
                $attributeSets[] = [];
            }
        } else {
            throw new \UnexpectedValueException("Expected array or int");
        }
        $user = User::factory()->createOne();
        $baseDate = $this->faker->dateTimeThisYear;
        $iX = 0;

        $dives = [];
        if ($factory === null) {
            $factory = Dive::factory();
        }
        foreach ($attributeSets as $attributeSet) {
            $baseData = [ 'date' => (new Carbon($baseDate))->addHours($iX++) ];
            /** @var Dive $dive */
            $dive = $factory->createOne(array_merge($baseData, $attributeSet));
            $dive->user_id = $user->id;
            $dive->user = $user;
            $dives[] = $dive;
        }

        return $dives;
    }
}
