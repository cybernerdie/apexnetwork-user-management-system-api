<?php

namespace App\Actions\User;

use App\Models\User;

class GetUserAction
{
    /**
     * Get a user.
     * 
     * @param  \App\Models\User  $user
     * @return \App\Models\User
     */
    public function execute(User $user)
    {
        return $user;
    }
}
