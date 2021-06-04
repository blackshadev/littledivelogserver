<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Repositories;

use App\CommandObjects\FindPlaceCommand;
use App\Domain\DataTransferObjects\PlaceData;
use App\Error\PlaceNotFound;
use App\Helpers\Explorer\Utilities;
use App\Models\Place;
use App\Models\User;
use App\Services\Repositories\PlaceRepository;
use DMS\PHPUnitExtensions\ArraySubset\Assert;
use Illuminate\Foundation\Testing\WithFaker;
use JeroenG\Explorer\Infrastructure\Scout\ScoutSearchCommandBuilder;
use Laravel\Scout\Builder;
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

        $this->repository->expects('findById')
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

        $this->repository->expects('findById')
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
                /** @var Place $arg */
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

    public function testItFindsDivesByDetails()
    {
        $searchCmd = new FindPlaceCommand();
        $searchCmd->setCountry('UK');
        $searchCmd->setKeywords('test');

        $this->repository->expects('search')->withArgs(
            function ($arg) {
                /** @var Builder $arg */
                $cmd = ScoutSearchCommandBuilder::wrap($arg);
                $filter = Utilities::toArray($cmd->getFilter());
                self::assertEquals([[
                    'term' => [ 'country_code' => [ 'value' => 'UK', 'boost' => null ] ]
                ]], $filter);

                $must = Utilities::toArray($cmd->getMust());

                Assert::assertArraySubset([[
                    'bool' => [
                        'should' => [[
                        'match' => [ 'name' => [ 'query' => 'test', 'fuzziness' => 'auto' ] ]
                    ], [
                        'match' => [ 'country' => [ 'query' => 'test', 'fuzziness' => 'auto'] ]
                    ]]
                ]]], $must);

                return true;
            }
        );

        $this->repository->find($searchCmd);
    }
}
