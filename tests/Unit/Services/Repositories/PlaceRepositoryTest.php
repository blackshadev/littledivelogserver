<?php

namespace Tests\Unit\Services\Repositories;

use App\DataTransferObjects\PlaceData;
use App\Error\PlaceNotFound;
use App\Models\Place;
use App\Models\User;
use App\Services\Repositories\PlaceRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Tests\TestCase;

class PlaceRepositoryTest extends TestCase
{
    use WithFaker;

    /** @var PlaceRepository|MockInterface */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = \Mockery::mock(PlaceRepository::class)->makePartial();
        $this->repository->shouldReceive('save')->byDefault();
    }

    public function testFindOrCreateThrowsExceptionWhenPlaceNotFoundById()
    {
        $id = $this->faker->numberBetween();

        $user = new User();

        $placeData = new PlaceData();
        $placeData->setId($id);

        $this->repository->expects('find')
            ->with($id)
            ->andReturnNull();

        $this->expectException(PlaceNotFound::class);

        $this->repository->findOrCreate($placeData, $user);
    }

    public function testFindOrCreateReturnsPlaceById()
    {
        $id = $this->faker->numberBetween();

        $user = new User();

        $placeData = new PlaceData();
        $placeData->setId($id);

        $place = new Place();

        $this->repository->expects('find')
            ->with($id)
            ->andReturn($place);

        $result = $this->repository->findOrCreate($placeData, $user);

        self::assertSame($place, $result);
    }

    public function testFindOrCreateReturnsPlaceByCountryAndName()
    {
        $name = $this->faker->city;
        $country = $this->faker->countryCode;

        $user = new User();

        $placeData = new PlaceData();
        $placeData->setName($name);
        $placeData->setCountryCode($country);

        $place = new Place();

        $this->repository->expects('findByName')
            ->with($country, $name)
            ->andReturn($place);

        $result = $this->repository->findOrCreate($placeData, $user);

        self::assertSame($place, $result);
    }

    public function testFindOrCreateCreatesNewPlace()
    {
        $name = $this->faker->city;
        $country = $this->faker->countryCode;

        $user = new User();

        $placeData = new PlaceData();
        $placeData->setName($name);
        $placeData->setCountryCode($country);

        $place = new Place();

        $this->repository->expects('findByName')
            ->with($country, $name)
            ->andReturnNull();

        $this->repository->expects('create')
            ->with($placeData, $user)
            ->andReturn($place);

        $result = $this->repository->findOrCreate($placeData, $user);

        self::assertSame($place, $result);
    }

    public function testItCreatesPlace()
    {
        $name = $this->faker->city;
        $country = $this->faker->countryCode;

        $user = new User();

        $placeData = new PlaceData();
        $placeData->setName($name);
        $placeData->setCountryCode($country);

        $this->repository->expects('save')
            ->withArgs(function ($arg) use ($placeData, $user) {
                /* @var Place $arg */
                self::assertInstanceOf(Place::class, $arg);
                self::assertEquals($placeData->getCountryCode(), $arg->country_code);
                self::assertEquals($placeData->getName(), $arg->name);
                self::assertEquals($user->id, $arg->created_by);

                return true;
            });

        $result = $this->repository->create($placeData, $user);

        self::assertInstanceOf(Place::class, $result);
        self::assertEquals($placeData->getCountryCode(), $result->country_code);
        self::assertEquals($placeData->getName(), $result->name);
        self::assertEquals($user->id, $result->created_by);
    }
}
