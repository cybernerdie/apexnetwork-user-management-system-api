<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\BaseTestCase;

class UserControllerTest extends BaseTestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private User $regularUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole(RoleEnum::ADMIN);

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole(RoleEnum::USER);
    }

    #[Test]
    public function authorized_user_can_create_new_user()
    {
        // Given an authenticated admin user
        $accessToken = $this->loginAndGetAccessToken($this->adminUser->email, 'password');

        $userData = [
            'name' => 'John Doe',
            'email' => '0GnF0@example.com',
            'password' => 'password',
            'role' => RoleEnum::USER,
        ];

        // When the admin user creates a new user with the role 'USER'
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->postJson('/api/v1/users', $userData);

        // Then the new user is created successfully and stored in the database
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User created successfully',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'roles',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
    }

    #[Test]
    public function unauthorized_user_cannot_create_new_user()
    {
        // Given an authenticated regular user
        $accessToken = $this->loginAndGetAccessToken($this->regularUser->email, 'password');

        // When the regular user tries to create a new user
        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'role' => RoleEnum::USER,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->postJson('/api/v1/users', $userData);

        // Then the request is denied with a 403 Forbidden status
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You are unauthorized to create user.',
            ]);
    }

    #[Test]
    public function invalid_user_data_results_in_validation_error()
    {
        // Given an authenticated admin user
        $accessToken = $this->loginAndGetAccessToken($this->adminUser->email, 'password');

        // When the admin user tries to create a new user with invalid data
        $invalidUserData = [
            'name' => 'Invalid User',
            'password' => 'password',
            'role' => RoleEnum::USER,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->postJson('/api/v1/users', $invalidUserData);

        // Then the request fails with a 422 Unprocessable Entity status due to validation errors
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function admin_can_view_user()
    {
        // Given an authenticated admin user
        $accessToken = $this->loginAndGetAccessToken($this->adminUser->email, 'password');

        // When the admin user requests to view another user's information
        $userToView = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->getJson('/api/v1/users/' . $userToView->uuid);

        // Then the user information is retrieved successfully
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User retrieved successfully',
                'data' => [
                    'id' => $userToView->uuid,
                ],
            ]);
    }

    #[Test]
    public function user_can_view_own_information()
    {
        // Given an authenticated regular user
        $accessToken = $this->loginAndGetAccessToken($this->regularUser->email, 'password');

        // When the regular user requests to view their own information
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->getJson('/api/v1/users/' . $this->regularUser->uuid);

        // Then the user information is retrieved successfully
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User retrieved successfully',
                'data' => [
                    'id' => $this->regularUser->uuid,
                ],
            ]);
    }

    #[Test]
    public function unauthorized_user_cannot_view_user()
    {
        // Given an authenticated regular user
        $accessToken = $this->loginAndGetAccessToken($this->regularUser->email, 'password');

        // When the regular user tries to view another user's information
        $userToView = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->getJson('/api/v1/users/' . $userToView->uuid);

        // Then the request is denied with a 403 Forbidden status
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You are unauthorized to view user.',
            ]);
    }

    #[Test]
    public function admin_can_update_user()
    {
        // Given an authenticated admin user
        $accessToken = $this->loginAndGetAccessToken($this->adminUser->email, 'password');

        // When the admin user updates another user's information
        $userToUpdate = User::factory()->create();
        $updatedUserData = [
            'name' => 'Updated Name',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->putJson('/api/v1/users/' . $userToUpdate->uuid, $updatedUserData);

        // Then the user information is updated successfully
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User updated successfully',
                'data' => [
                    'id' => $userToUpdate->uuid,
                    'name' => 'Updated Name',
                    // Add other updated fields here...
                ],
            ]);

        // Ensure user information is updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $userToUpdate->id,
            'name' => 'Updated Name',
        ]);
    }

    #[Test]
    public function user_can_update_own_information()
    {
        // Given an authenticated regular user
        $accessToken = $this->loginAndGetAccessToken($this->regularUser->email, 'password');

        // When the regular user updates their own information
        $updatedUserData = [
            'name' => 'Updated Name',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->putJson('/api/v1/users/' . $this->regularUser->uuid, $updatedUserData);

        // Then the user information is updated successfully
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User updated successfully',
                'data' => [
                    'id' => $this->regularUser->uuid,
                    'name' => 'Updated Name',
                ],
            ]);

        // Ensure user information is updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $this->regularUser->id,
            'name' => 'Updated Name',
        ]);
    }

    #[Test]
    public function unauthorized_user_cannot_update_user()
    {
        // Given an authenticated regular user
        $accessToken = $this->loginAndGetAccessToken($this->regularUser->email, 'password');

        // When the regular user tries to update another user's information
        $userToUpdate = User::factory()->create();
        $updatedUserData = [
            'name' => 'Updated Name',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->putJson('/api/v1/users/' . $userToUpdate->uuid, $updatedUserData);

        // Then the request is denied with a 403 Forbidden status
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You are unauthorized to update user.',
            ]);

        // Ensure user information remains unchanged in the database
        $this->assertDatabaseMissing('users', [
            'id' => $userToUpdate->id,
            'name' => 'Updated Name',
        ]);
    }


    #[Test]
    public function authorized_user_can_delete_user()
    {
        // Given an authenticated admin user
        $accessToken = $this->loginAndGetAccessToken($this->adminUser->email, 'password');

        // When the admin user tries to delete another user
        $userToDelete = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->deleteJson('/api/v1/users/' . $userToDelete->uuid);

        // Then the user is deleted successfully
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User deleted successfully',
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $userToDelete->id,
        ]);
    }

    #[Test]
    public function unauthorized_user_cannot_delete_user()
    {
        // Given an authenticated regular user
        $accessToken = $this->loginAndGetAccessToken($this->regularUser->email, 'password');

        // When the regular user tries to delete another user
        $userToDelete = User::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->deleteJson('/api/v1/users/' . $userToDelete->uuid);

        // Then the request is denied with a 403 Forbidden status
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You are unauthorized to delete user.',
            ]);
    }
}
