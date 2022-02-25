<?php

declare(strict_types=1);

namespace Tests\Integration\Api;

use App\Http\Controllers\Api\DiveController;
use App\Models\Buddy;
use App\Models\Dive;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\WithFakeTAuthentication;

final class DiveShowTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    use WithFakeTAuthentication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakedTauth();
    }

    public function testDiveListForAuthenticatedUser(): void
    {
        /** @var User $user */
        $user = User::factory()
            ->has(Tag::factory()->count(10))
            ->has(Buddy::factory()->count(10))
            ->createOne();

        $this->fakeAccessTokenFor($user);

        $dives = Dive::factory()->count(10)->for($user)->filled()->create();

        $this->fakeAccessTokenFor($user);

        $this->get(action([DiveController::class, 'show'], $dives[0]->id), ['Authorization' => 'Bearer aa.test.aa'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'dive_id',
                'date',
                'divetime',
                'max_depth',
                'place' => ['place_id', 'name', 'country_code'],
                'tags' => [['tag_id', 'text', 'color']],
                'buddies' => [['buddy_id', 'text', 'color']],
                'tanks',
            ]);
    }
}
