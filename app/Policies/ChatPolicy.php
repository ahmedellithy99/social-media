<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;

class ChatPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user , Chat $chat)
    {
        return $user->id === $chat->A || $user->id === $chat->B;
    }

    public function create(User $user , Chat $chat)
    {
        return $user->id === $chat->A || $user->id === $chat->B;
    }
}
