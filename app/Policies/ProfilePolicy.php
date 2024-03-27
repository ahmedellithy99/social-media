<?php

namespace App\Policies;

use App\Models\User;

class ProfilePolicy
{
    /**
     * Create a new policy instance.
     */
    public function updateImage(User $authUser , User $visitedUser)
    {

        return $authUser->id == $visitedUser->id ;
    }

    public function update(User $authUser , User $visitedUser)
    {
        return $authUser->id == $visitedUser->id ;
    }

    public function delete(User $authUser , User $visitedUser)
    {
        return $authUser->id == $visitedUser->id ;
    }
}
