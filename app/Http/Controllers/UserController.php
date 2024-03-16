<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function follow(User $user)
    {
        
        Follower::create([
            'user_id' => $user->id,
            'follower_id' => auth()->user()->id
        ]);

        return back();

    }

    public function unfollow(User $user)
    {
        
        Follower::where('user_id' , $user->id)
                ->where('follower_id' , auth()->user()->id)->delete();

        return back();

    }
}
