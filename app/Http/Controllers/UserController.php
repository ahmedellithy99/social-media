<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\User;
use App\Notifications\FollowNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    public function follow(User $user)
    {
        $authUser = auth()->user();
        
        Follower::create([
            'user_id' => $user->id,
            'follower_id' => auth()->user()->id
        ]);
        
        Notification::send($user , new FollowNotification($authUser->name , $authUser->username , $authUser->id));

        return back();

    }

    public function unfollow(User $user)
    {
        
        Follower::where('user_id' , $user->id)
                ->where('follower_id' , auth()->user()->id)->delete();

        return back();

    }


}
