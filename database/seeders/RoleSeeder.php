<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = RoleEnum::getValues();

        collect($roles)->each(fn ($role) => \Spatie\Permission\Models\Role::updateOrCreate(['name' => $role, 'guard_name' => 'api']));
    }
}
