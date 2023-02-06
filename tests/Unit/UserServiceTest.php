<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use Spatie\LaravelIgnition\Support\Composer\FakeComposer;

class UserServiceTest extends TestCase
{
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function create_user_with_valid_data_test() 
    {
        $serviceMock = $this->createPartialMock(UserService::class, ['createUser']);
        serviceMock->shouldRecve()
    }
}
