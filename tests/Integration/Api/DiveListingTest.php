<?php

declare(strict_types=1);

namespace Tests\Integration\Api;

use App\Models\Dive;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\WithFakeTAuthentication;

class DiveListingTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;
    use WithFakeTAuthentication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakedTauth();
    }

    public function testDiveListForAuthenticatedUser()
    {
        $diveCount = $this->faker->numberBetween(1, 10);

        /** @var User $user */
        $user = User::factory()
            ->has(Tag::factory()->count(10))
            ->createOne();

        Dive::factory()->count($diveCount)->for($user)->filled()->create();

        $this->fakeAccessTokenFor($user);

        $response = $this->get('/api/dives/', ['Authorization' => 'Bearer aa.test.aa']);
        $response->assertStatus(200);

        $response->assertJsonStructure([[
            'dive_id',
            'date',
            'divetime',
            'place' => [ 'place_id', 'name', 'country_code'],
            'tags' => [['tag_id', 'text', 'color']]
        ]]);
    }
}
