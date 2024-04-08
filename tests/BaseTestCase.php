<?php

namespace Tests;

use App\Enums\RoleEnum;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

class BaseTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', config('app.url')
        );

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roles = RoleEnum::getValues();
        collect($roles)->each(fn($role) => \Spatie\Permission\Models\Role::updateOrCreate(['name' => $role, 'guard_name' => 'api']));
    }

    /**
     * Login and get access token
     * 
     * @param string $email
     * @param string $password
     * @return string
     */
    public function loginAndGetAccessToken($email, $password)
    {
        $loginData = ['email' => $email, 'password' => $password];
        $response = $this->postJson('/api/v1/login', $loginData);

        return $response->json()['data']['access_token'];
    }
}
