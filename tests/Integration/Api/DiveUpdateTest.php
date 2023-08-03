<?php

declare(strict_types=1);

namespace Tests\Integration\Api;

use App\Domain\Support\Arrg;
use App\Http\Controllers\Api\DiveController;
use App\Models\Computer;
use App\Models\Dive;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\WithFakeTAuthentication;

final class DiveUpdateTest extends TestCase
{
    use DatabaseTransactions;
    use WithFakeTAuthentication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakedTauth();
    }

    public function testItWorksWithMinimalData(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $this->fakeAccessTokenFor($user);

        $dive = Dive::factory()->state([
            'user_id' => $user->id,
        ])->createOne();

        $data = [
            'date' => Carbon::now()->format(DateTimeInterface::ATOM),
            'divetime' => $dive->divetime + 15,
            'max_depth' => $dive->max_depth + 1,
        ];

        $this->put(action([DiveController::class, 'update'], [$dive->id]), $data)
            ->assertStatus(200)
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('dives', array_merge($data, ['user_id' => $user->id]));
    }

    public function testItRequiresToBeLoggedIn(): void
    {
        $user = User::factory()->createOne();
        $dive = Dive::factory()->state([
            'user_id' => $user->id,
        ])->createOne();

        $data = [
            'date' => $dive->date,
            'divetime' => $dive->divetime + 15,
            'max_depth' => $dive->max_depth + 1,
        ];

        $this->put(action([DiveController::class, 'update'], [$dive->id]), $data)
            ->assertStatus(403);

        $this->assertDatabaseMissing('dives', array_merge($data, ['user_id' => $user->id]));
    }

    public function testUpdateWithFullData(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $this->fakeAccessTokenFor($user);

        $dive = Dive::factory()->state([
            'user_id' => $user->id,
        ])->createOne();
        $flatFields = ['date', 'divetime', 'max_depth'];

        $data = [
            'date' => Carbon::now()->format(DateTimeInterface::ATOM),
            'divetime' => $dive->divetime + 15,
            'max_depth' => $dive->max_depth + 1,
            'buddies' => [
                [
                    'text' => 'test',
                    'color' => '#000000',
                ]
            ],
            'tags' => [
                [
                    'text' => 'test',
                    'color' => '#000000',
                ]
            ],
            'place' => [
                'country_code' => 'NL',
                'name' => 'new place',
            ],
            'tanks' => [
                [
                    'pressure' => [
                        'begin' => 201,
                        'end' => 42,
                    ],
                    'volume' => 12,
                    'oxygen' => 21
                ]
            ],
            'samples' => [
                ['Time' => 0, 'Depth' => 0],
                ['Time' => 15, 'Depth' => 5],
                ['Time' => 30, 'Depth' => 0]
            ],
        ];

        $resp = $this->put(action([DiveController::class, 'update'], [$dive->id]), $data)
            ->assertStatus(200)
            ->assertJsonStructure(['dive_id'])
            ->assertJsonFragment(Arrg::only($data, $flatFields));

        $diveId = $resp->decodeResponseJson()['dive_id'];

        $databaseData = array_merge(
            Arrg::only($data, $flatFields),
            ['user_id' => $user->id]
        );

        $this->assertDatabaseHas('dives', $databaseData);

        $this->assertDatabaseHas('dive_tanks', [
            'dive_id' => $diveId,
            'volume' => $data['tanks'][0]['volume'],
            'oxygen' => $data['tanks'][0]['oxygen'],
            'pressure_begin' => $data['tanks'][0]['pressure']['begin'],
            'pressure_end' => $data['tanks'][0]['pressure']['end'],
            'pressure_type' => 'bar',
        ]);

        $this->assertDatabaseHas('places', [
            'country_code' => 'NL',
            'name' => $data['place']['name'],
            'created_by' => $user->id
        ]);

        $this->assertDatabaseHas('buddies', [
            'name' => $data['buddies'][0]['text'],
            'color' => $data['buddies'][0]['color'],
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('tags', [
            'text' => $data['buddies'][0]['text'],
            'color' => $data['buddies'][0]['color'],
            'user_id' => $user->id
        ]);
    }

    public function testItDoesntUpdateComputerData(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $this->fakeAccessTokenFor($user);

        $dive = Dive::factory()->state([
            'user_id' => $user->id,
        ])->createOne();

        $computerData = [
            'user_id' => $user->id,
            'last_read' => '2021-02-03T14:13:12Z',
            'last_fingerprint' => 'xxx',
        ];
        /** @var Computer $computer */
        $computer = Computer::factory()->state([
            'user_id' => $user->id,
            'last_read' => '2021-02-03T14:13:12Z',
            'last_fingerprint' => 'xxx',
        ])->createOne();

        $data = [
            ...$dive->attributesToArray(),
            'computer_id' => $computer->id,
        ];

        $this->put(action([DiveController::class, 'update'], [$dive->id]), $data)
            ->assertStatus(200)
            ->assertJsonFragment([
                'dive_id' => $dive->id,
                'computer' => [
                    'computer_id' => $computer->id,
                    'name' => $computer->name,
                    'vendor' => $computer->vendor,
                ],
            ]);

        $this->assertDatabaseHas('dives', array_merge($data, ['user_id' => $user->id]));
        $this->assertDatabaseHas('computers', array_merge(['id' => $computer->id], $computerData));
    }
}
