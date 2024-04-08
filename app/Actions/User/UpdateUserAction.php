<?php

namespace App\Actions\User;

use App\Models\User;

class UpdateUserAction
{
    /**
     * Update a user.
     * 
     * @param  \App\Models\User  $user
     * @param  array  $data
     * @return \App\Models\User
     */
    public function execute(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }
}
