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

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakedTauth();
    }

    public function testItListsComputers(): void
    {
        /** @var User $user */
        $user = User::factory()
            ->has(Computer::factory()->count(3))
            ->createOne();
        $this->fakeAccessTokenFor($user);

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
}
