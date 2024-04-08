<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    /**
     * Create a user.
     * 
     * @param  array  $data
     * @return \App\Models\User
     */
    public function execute(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            
            $user->assignRole($data['role']);
            
            return $user;
        });
    }
}
