<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\BaseTestCase;

class AuthControllerTest extends BaseTestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_register_successfully()
    {
        // Given valid registration data
        $userData = [
            'name' => 'John Doe',
            'email' => 'john1@example.com',
            'password' => 'password',
        ];

        // When a user attempts to register
        $response = $this->postJson('/api/v1/register', $userData);

        // Then the user is registered successfully
        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data' => ['access_token']]);
    }

    #[Test]
    public function user_registration_fails_with_incomplete_details()
    {
        // Given incomplete registration data
        $userData = [
            'name' => 'John Doe',
        ];

        // When a user attempts to register with incomplete details
        $response = $this->postJson('/api/v1/register', $userData);

        // Then the registration fails with a 422 Unprocessable Entity status due to validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    #[Test]
    public function user_login_fails_with_invalid_details()
    {
        // Given a registered user
        $user = User::factory()->create(['password' => bcrypt('password')]);

        // When the user attempts to login with invalid details (wrong password)
        $loginData = ['email' => $user->email, 'password' => 'wrong_password'];
        $response = $this->postJson('/api/v1/login', $loginData);

        // Then the login fails with a 401 Unauthorized status
        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    #[Test]
    public function user_can_login_successfully()
    {
        // Given a registered user
        $user = User::factory()->create(['password' => bcrypt('password')]);

        // When the user attempts to login
        $loginData = ['email' => $user->email, 'password' => 'password'];
        $response = $this->postJson('/api/v1/login', $loginData);

        // Then the user is logged in successfully
        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data' => ['access_token']]);
    }

    #[Test]
    public function authenticated_user_can_logout()
    {
        // Given an authenticated user
        $user = User::factory()->create();
        $user->assignRole(RoleEnum::USER);
        $accessToken = $this->loginAndGetAccessToken($user->email, 'password');

        // When the user attempts to logout
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $accessToken])->postJson('/api/v1/logout');

        // Then the user is logged out successfully
        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);
    }
}
