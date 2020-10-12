<?php

namespace Tests\Unit\Services\Repositories;

use App\DataTransferObjects\TankData;
use App\Models\DiveTank;
use App\Services\Repositories\TankRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Tests\TestCase;

class TankRepositoryTest extends TestCase
{
    use WithFaker;

    /** @var TankRepository|MockInterface */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = \Mockery::mock(TankRepository::class)
            ->makePartial();

        $this->repository->shouldReceive('save')->byDefault();
    }

    public function testItMakesANewTank()
    {
        $volume = $this->faker->numberBetween(7, 12);
        $oxygen = $this->faker->randomElement([21, 32, 37, 39, 41]);
        $begin = $this->faker->numberBetween(110, 210);
        $end = $this->faker->numberBetween(40, $begin);
        $type = 'bar';

        $diveTank = new TankData();
        $diveTank->setVolume($volume);
        $diveTank->setOxygen($oxygen);
        $diveTank->getPressures()->setBegin($begin);
        $diveTank->getPressures()->setEnd($end);
        $diveTank->getPressures()->setType('bar');

        $this->repository->expects('save')->never();

        $result = $this->repository->make($diveTank);

        self::assertEquals($volume, $result->volume);
        self::assertEquals($oxygen, $result->oxygen);
        self::assertEquals($begin, $result->pressure_begin);
        self::assertEquals($end, $result->pressure_end);
        self::assertEquals($type, $result->pressure_type);
    }

    public function testItUpdatesANewTank()
    {
        $volume = $this->faker->numberBetween(7, 12);
        $oxygen = $this->faker->randomElement([21, 32, 37, 39, 41]);
        $begin = $this->faker->numberBetween(110, 210);
        $end = $this->faker->numberBetween(40, $begin);
        $type = 'bar';

        $tank = new DiveTank();

        $tankData = new TankData();
        $tankData->setVolume($volume);
        $tankData->setOxygen($oxygen);
        $tankData->getPressures()->setBegin($begin);
        $tankData->getPressures()->setEnd($end);
        $tankData->getPressures()->setType('bar');

        $this->repository->expects('save')->with($tank);

        $this->repository->update($tank, $tankData);

        self::assertEquals($volume, $tank->volume);
        self::assertEquals($oxygen, $tank->oxygen);
        self::assertEquals($begin, $tank->pressure_begin);
        self::assertEquals($end, $tank->pressure_end);
        self::assertEquals($type, $tank->pressure_type);
    }
}
