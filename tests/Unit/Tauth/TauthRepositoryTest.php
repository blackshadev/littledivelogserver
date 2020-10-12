<?php

namespace Tests\Unit\Tauth;

use App\Models\User;
use App\Services\Repositories\TauthRepository;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Littledev\Tauth\Services\JWTServiceInterface;
use Littledev\Tauth\Services\TauthRepositoryInterface;
use stdClass;
use Tests\TestCase;

class TauthRepositoryTest extends TestCase
{
    use WithFaker;

    private JWTServiceInterface $jwtService;
    private TauthRepositoryInterface $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jwtService = \Mockery::mock(JWTServiceInterface::class);

        $this->subject = new TauthRepository($this->jwtService);
    }

    public function testFindUserByCredentialsWithValidCredentials()
    {
        $data = [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ];
        $user = new User();

        Auth::shouldReceive('once')->with($data)->andReturnTrue();
        Auth::shouldReceive('user')->andReturn($user);

        $result = $this->subject->findUserByCredentials($data);

        self::assertSame($user, $result);
    }

    public function testFindUserByCredentialsWithInvalidCredentials()
    {
        $data = [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ];

        Auth::shouldReceive('once')->with($data)->andReturnFalse();
        Auth::shouldReceive('user')->andReturnNull();

        $result = $this->subject->findUserByCredentials($data);

        self::assertNull($result);
    }

    public function testFindUserByCredentialsWithInvalidUserModel()
    {
        $data = [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ];

        Auth::shouldReceive('once')->with($data)->andReturnFalse();
        Auth::shouldReceive('user')->andReturn(new StdClass());

        $this->expectException(\UnexpectedValueException::class);
        $result = $this->subject->findUserByCredentials($data);

        self::assertNull($result);
    }
}
