<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Repositories;

use App\Domain\DataTransferObjects\EquipmentData;
use App\Domain\DataTransferObjects\TankData;
use App\Models\Equipment;
use App\Models\EquipmentTank;
use App\Services\Repositories\EquipmentRepository;
use App\Services\Repositories\EquipmentTankRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class EquipmentRepositoryTest extends TestCase
{
    use WithFaker;

    /** @var Mockery\MockInterface|EquipmentRepository  */
    private $equipmentRepository;

    /**
     * @var EquipmentTankRepository|Mockery\MockInterface
     */
    private $tankRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tankRepository = Mockery::mock(EquipmentTankRepository::class);
        $this->equipmentRepository = Mockery::mock(EquipmentRepository::class, [$this->tankRepository])
            ->makePartial();

        $this->equipmentRepository->shouldReceive('save')->byDefault();
    }

    public function testItStoresNewTanks()
    {
        $userId = 1;
        $tankData = new TankData();

        $data = new EquipmentData();
        $data->setTanks([$tankData]);
        $data->setUserId($userId);

        $equipment = $this->newEquipment([], $userId);

        $tank = new EquipmentTank();

        $this->equipmentRepository->expects('appendTank')->with($equipment, $tank);
        $this->tankRepository->expects('make')->with($tankData)->andReturn($tank);
        $this->equipmentRepository->update($equipment, $data);
    }

    public function testItRemovesOldTanks()
    {
        $userId = 1;
        $tank = new EquipmentTank();

        $equipment = $this->newEquipment([$tank], $userId);

        $data = new EquipmentData();
        $data->setUserId($userId);
        $data->setTanks([]);

        $this->equipmentRepository->expects('removeTank')->with($equipment, $tank);

        $this->equipmentRepository->update($equipment, $data);
    }

    public function testItUpdatesTanks()
    {
        $userId = 1;

        $tankData = new TankData();
        $tankData->setOxygen($this->faker->randomElement([21, 32, 39, 41]));
        $tankData->setVolume($this->faker->randomElement([7, 9, 10, 12]));
        $tankData->getPressures()->setType($this->faker->randomElement(['bar', 'psi']));
        $tankData->getPressures()->setBegin($this->faker->numberBetween(110, 210));
        $tankData->getPressures()->setEnd($this->faker->numberBetween(40, $tankData->getPressures()->getBegin()));
        $tankData->getPressures()->setType('bar');

        $tank = new EquipmentTank();

        $equipment = $this->newEquipment([$tank], $userId);

        $data = new EquipmentData();
        $data->setTanks([$tankData]);
        $data->setUserId($userId);

        $this->tankRepository->expects('update')->with($tank, $tankData);

        $this->equipmentRepository->update($equipment, $data);
    }

    /** @param EquipmentTank[] $tanks */
    private function newEquipment(array $tanks, int $userId = 1): Equipment
    {
        $equipment = new Equipment();
        $equipment->user_id = $userId;
        $equipment->tanks = $tanks;

        return $equipment;
    }
}
