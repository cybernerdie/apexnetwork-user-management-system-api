<?php

namespace App\Actions\User;

use App\Models\User;

class DeleteUserAction
{
    /**
     * Delete a user.
     * 
     * @param  \App\Models\User  $user
     * @return void
     */
    public function execute(User $user)
    {
        $user->delete();
    }
}
