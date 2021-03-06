<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Repositories;

use App\CommandObjects\FindDivesCommand;
use App\DataTransferObjects\BuddyData;
use App\DataTransferObjects\DiveData;
use App\DataTransferObjects\TagData;
use App\DataTransferObjects\TankData;
use App\Helpers\Explorer\Utilities;
use App\Models\Buddy;
use App\Models\Computer;
use App\Models\Dive;
use App\Models\DiveTank;
use App\Models\Place;
use App\Models\Tag;
use App\Models\User;
use App\Services\Repositories\BuddyRepository;
use App\Services\Repositories\ComputerRepository;
use App\Services\Repositories\DiveRepository;
use App\Services\Repositories\DiveTankRepository;
use App\Services\Repositories\PlaceRepository;
use App\Services\Repositories\TagRepository;
use Carbon\Carbon;
use DMS\PHPUnitExtensions\ArraySubset\Assert;
use Illuminate\Foundation\Testing\WithFaker;
use JeroenG\Explorer\Application\BuildCommand;
use Laravel\Scout\Builder;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class DiveRepositoryTest extends TestCase
{
    use WithFaker;

    /**
     * @var DiveRepository|MockInterface
     */
    private $diveRepository;

    /**
     * @var PlaceRepository|MockInterface
     */
    private $placeRepository;

    /**
     * @var MockInterface
     */
    private $tagRepository;

    /**
     * @var BuddyRepository|MockInterface
     */
    private $buddyRepository;

    /**
     * @var DiveTankRepository|MockInterface
     */
    private $tankRepository;

    /**
     * @var ComputerRepository|MockInterface
     */
    private $computerRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->placeRepository = Mockery::mock(PlaceRepository::class);
        $this->tagRepository = Mockery::mock(TagRepository::class);
        $this->buddyRepository = Mockery::mock(BuddyRepository::class);
        $this->tankRepository = Mockery::mock(DiveTankRepository::class);
        $this->computerRepository = Mockery::mock(ComputerRepository::class);

        $this->diveRepository = Mockery::mock(DiveRepository::class, [
            $this->placeRepository,
            $this->buddyRepository,
            $this->tagRepository,
            $this->tankRepository,
            $this->computerRepository,
        ])->makePartial();

        $this->diveRepository->shouldReceive('save')->byDefault();
        $this->diveRepository->shouldReceive('search')->byDefault();
        $this->diveRepository->shouldReceive('attachTags')->byDefault();
        $this->diveRepository->shouldReceive('attachBuddies')->byDefault();
        $this->diveRepository->shouldReceive('removeTank')->byDefault();
        $this->diveRepository->shouldReceive('appendTank')->byDefault();
    }

    public function testItUpdatesDives()
    {
        $dive = new Dive();
        $data = new DiveData();
        $data->setDate(new Carbon($this->faker->dateTime));
        $data->setDivetime($this->faker->numberBetween(0, 3200));
        $data->setMaxDepth($this->faker->randomFloat(3, 0, 30));

        $this->diveRepository->update($dive, $data);

        self::assertEquals($data->getMaxDepth(), $dive->max_depth);
        self::assertEquals($data->getDivetime(), $dive->divetime);
        self::assertEquals($data->getDate(), $dive->date);
    }

    public function testItDoesNotCallRepositoriesOnNull()
    {
        $dive = new Dive();
        $data = new DiveData();

        $this->placeRepository->shouldNotReceive('findOrCreate');
        $this->tagRepository->shouldNotReceive('findOrCreate');
        $this->buddyRepository->shouldNotReceive('findOrCreate');
        $this->tankRepository->shouldNotReceive('findOrCreate');
        $this->computerRepository->shouldNotReceive('findOrCreate');

        $this->diveRepository->update($dive, $data);
    }

    public function testItFindsOrCreatesPlace()
    {
        $place = new Place();

        $user = new User();
        $dive = new Dive();
        $dive->user = $user;

        $data = new DiveData();
        $data->getPlace()->setName($this->faker->word);
        $data->getPlace()->setCountryCode($this->faker->countryCode);

        $this->placeRepository->expects('findOrCreate')->with(
            $data->getPlace(),
            $user
        )->andReturn($place);

        $this->diveRepository->update($dive, $data);

        self::assertEquals($place, $dive->place);
    }

    public function testItFindsOrCreatesTags()
    {
        $tagData = new TagData();
        $tag = new Tag();

        $user = new User();
        $dive = new Dive();
        $dive->user = $user;
        $data = new DiveData();
        $data->setTags([$tagData]);

        $this->tagRepository->expects('findOrCreate')
            ->with($tagData, $user)
            ->andReturn($tag);

        $this->diveRepository->expects('attachTags')->with($dive, [$tag]);
        $this->diveRepository->update($dive, $data);
    }

    public function testItFindsOrCreatesBuddies()
    {
        $buddyData = new BuddyData();
        $buddy = new Buddy();

        $user = new User();
        $dive = new Dive();
        $dive->user = $user;
        $data = new DiveData();
        $data->setBuddies([$buddyData]);

        $this->buddyRepository->expects('findOrCreate')
            ->with($buddyData, $user)
            ->andReturn($buddy);

        $this->diveRepository->expects('attachBuddies')->with($dive, [$buddy]);
        $this->diveRepository->update($dive, $data);
    }

    public function testItStoresNewTanks()
    {
        $tankData = new TankData();
        $tank = new DiveTank();

        $dive = new Dive();
        $data = new DiveData();
        $data->setTanks([$tankData]);

        $this->diveRepository->expects('appendTank')->with($dive, $tank);
        $this->tankRepository->expects('make')->with($tankData)->andReturn($tank);
        $this->diveRepository->update($dive, $data);
    }

    public function testItRemovesOldTanks()
    {
        $tank = new DiveTank();

        $dive = new Dive();
        $dive->tanks = [$tank];

        $data = new DiveData();
        $data->setTanks([]);

        $this->diveRepository->expects('removeTank')->with($dive, $tank);

        $this->diveRepository->update($dive, $data);
    }

    public function testItUpdatesTanks()
    {
        $tankData = new TankData();
        $tankData->setOxygen($this->faker->randomElement([21, 32, 39, 41]));
        $tankData->setVolume($this->faker->randomElement([7, 9, 10, 12]));
        $tankData->getPressures()->setType($this->faker->randomElement(['bar', 'psi']));
        $tankData->getPressures()->setBegin($this->faker->numberBetween(110, 210));
        $tankData->getPressures()->setEnd($this->faker->numberBetween(40, $tankData->getPressures()->getBegin()));
        $tankData->getPressures()->setType('bar');

        $tank = new DiveTank();

        $dive = new Dive();
        $dive->tanks = [$tank];

        $data = new DiveData();
        $data->setTanks([$tankData]);

        $this->tankRepository->expects('update')->with($tank, $tankData);

        $this->diveRepository->update($dive, $data);
    }

    public function testItUpdatesComputerLastRead()
    {
        $computer = new Computer();
        $computer->id = 1;
        $dive = new Dive();
        $dive->computer = $computer;

        $data = new DiveData();
        $data->setComputerId($computer->id);
        $data->setDate(new Carbon($this->faker->dateTime));
        $data->setFingerprint($this->faker->word);

        $this->computerRepository->expects('find')
            ->with($computer->id)
            ->andReturn($computer);

        $this->computerRepository->expects('updateLastRead')
            ->with($computer, $data->getDate(), $data->getFingerprint());

        $this->diveRepository->update($dive, $data);
    }

    public function testItFindsDivesByDetails()
    {
        $lastYear = Carbon::now()->subYear();
        $now = Carbon::now();
        $searchCmd = FindDivesCommand::forUser(-1);
        $searchCmd->setBuddies([1, 2]);
        $searchCmd->setTags([3, 4]);
        $searchCmd->setPlaceId(4);
        $searchCmd->setAfter($lastYear);
        $searchCmd->setBefore($now);

        $this->diveRepository->expects('search')->withArgs(
            function ($arg) use ($lastYear, $now) {
                /** @var Builder $arg */
                $cmd = BuildCommand::wrap($arg);
                $filter = Utilities::toArray($cmd->getFilter());
                self::assertEquals([[
                    'term' => [ 'user_id' => -1, 'boost' => null ]
                ]], $filter);

                $must = Utilities::toArray($cmd->getMust());

                Assert::assertArraySubset([[
                    'range' => [ 'date' => ['gt' => $lastYear]]
                ], [
                    'range' => [ 'date' => ['lt' => $now]]
                ], [
                    'nested' => [ 'path' => 'place', 'query' => ['term' => ['place.id' => 4 ]]]
                ], [
                    'nested' => [ 'path' => 'buddies', 'query' => ['term' => ['buddies.id' => [1, 2] ]]]
                ], [
                    'nested' => [ 'path' => 'tags', 'query' => ['term' => ['tags.id' => [3, 4] ]]]
                ]], $must);

                return true;
            }
        );

        $this->diveRepository->find($searchCmd);
    }
}
