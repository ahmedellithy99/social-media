<?php

namespace App\Http\Controllers;

use App\Models\Chat;
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
        
        $chatSubId = ($user->id > $authUser->id) ? (string)$authUser->id . (string) $user->id 
                                                :(string) $user->id .  (string) $authUser->id ;

        if(!Chat::where('subId' , $chatSubId)->exists()){
            Chat::create(['subId' => $chatSubId]);
        }
        
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
