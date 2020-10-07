<?php

namespace Tests\Unit\Repositories;

use App\DataTransferObjects\BuddyData;
use App\DataTransferObjects\DiveData;
use App\DataTransferObjects\TagData;
use App\DataTransferObjects\TankData;
use App\Models\Buddy;
use App\Models\Dive;
use App\Models\DiveTank;
use App\Models\Place;
use App\Models\Tag;
use App\Models\User;
use App\Services\Repositories\BuddyRepository;
use App\Services\Repositories\ComputerRepository;
use App\Services\Repositories\DiveRepository;
use App\Services\Repositories\PlaceRepository;
use App\Services\Repositories\TagRepository;
use App\Services\Repositories\TankRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Mockery;
use Mockery\MockInterface;

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
     * @var TankRepository|MockInterface
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
        $this->tankRepository = Mockery::mock(TankRepository::class);
        $this->computerRepository = Mockery::mock(ComputerRepository::class);

        $this->diveRepository = Mockery::mock(DiveRepository::class, [
            $this->placeRepository,
            $this->buddyRepository,
            $this->tagRepository,
            $this->tankRepository,
            $this->computerRepository
        ])->makePartial();

        $this->diveRepository->shouldReceive('save')->byDefault();
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

        $dive = new Dive();
        $data = new DiveData();
        $data->getPlace()->setName($this->faker->word);
        $data->getPlace()->setCountryCode($this->faker->countryCode);

        $this->placeRepository->expects('findOrCreate')->with($data->getPlace())->andReturn($place);

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

        $this->diveRepository->expects("appendTank")->with($dive, $tank);
        $this->tankRepository->expects('create')->with($tankData)->andReturn($tank);
        $this->diveRepository->update($dive, $data);
    }

    public function testShouldUpdateComputerLastRead()
    {

    }
}
