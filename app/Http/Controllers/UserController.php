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
        // assigning the smaller id to be the first , finally combine them .
        $chatSubId = ($user->id > $authUser->id) ? (string)$authUser->id . (string) $user->id 
                                                :(string) $user->id .  (string) $authUser->id ;

        if($user->id > $authUser->id)
        {
            $smallerId = (string)$authUser->id ; 
            $biggerId = (string)$user->id;
        }
        else
        {
            $smallerId = (string)$user->id;
            $biggerId = (string)$authUser->id;
        }
        
        $chatSubId = $smallerId . $biggerId ;

        if(!Chat::where('subId' , $chatSubId)->exists()){
            Chat::create([
                'subId' => $chatSubId , 
                'A' => $smallerId ,
                'B' => $biggerId
            ]);
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
