<?php

declare(strict_types=1);

namespace Tests\Integration\Api;

use App\Http\Controllers\Api\ComputerController;
use App\Models\Computer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\WithFakeTAuthentication;

class ComputerControllerTest extends TestCase
{
    use WithFaker;
    use WithFakeTAuthentication;
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakedTauth();

        $this->user = User::factory()
            ->has(Computer::factory()->count(3))
            ->createOne();
        $this->fakeAccessTokenFor($this->user);
    }

    public function testItListsComputers(): void
    {
        $resp = $this->get(action([ComputerController::class, 'index']))
           ->assertStatus(200);

        $data = $resp->json();
        self::assertIsArray($data);
        self::assertArrayHasKey('computer_id', $data[0]);
        self::assertArrayHasKey('serial', $data[0]);
        self::assertArrayHasKey('vendor', $data[0]);
        self::assertArrayHasKey('model', $data[0]);
        self::assertArrayHasKey('type', $data[0]);
        self::assertArrayHasKey('dive_count', $data[0]);
        self::assertArrayHasKey('last_read', $data[0]);
        self::assertArrayHasKey('last_fingerprint', $data[0]);
    }

    public function testItCreatesComputer(): void
    {
        $data = [
            "name" => "EON Steel",
            "serial" => 1234567890,
            "vendor" => "Suunto",
            "model" => 0,
            "type" => 65541
        ];

        $this->postJson(action([ComputerController::class, 'upsert']), $data)
            ->assertStatus(200)
            ->assertJsonStructure(['computer_id', 'name', 'serial', 'vendor', 'model', 'type']);

        $this->assertDatabaseHas(
            'computers',
            [ 'name' => 'EON Steel', 'serial' => 1234567890, 'vendor' => 'Suunto', 'model' => 0, 'type' => 65541 ]
        );
    }

    public function testItUpdatesComputer(): void
    {
        $computer = Computer::factory()->for($this->user)->createOne();

        $data = [
            "name" => ":test:",
            "serial" => $computer->serial,
            "vendor" => $computer->vendor,
            "model" => $computer->model,
            "type" => $computer->type
        ];

        $this->postJson(action([ComputerController::class, 'upsert']), $data)
            ->assertStatus(200)
            ->assertJsonStructure(['computer_id', 'name', 'serial', 'vendor', 'model', 'type']);

        $this->assertDatabaseHas(
            'computers',
            [
                'id' => $computer->id,
                'name' => ':test:'
            ]
        );
    }
}
