<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {   

        if(isset(auth()->user()->id)){
            
            $notifications = auth()->user()->notifications;

            // return NotificationResource::collection($notifications);
            $userId = auth()->user()->id;
            
            $posts = Post::items($userId)
                ->join('followers AS f' , function ($join) use ($userId) {
                $join->on('posts.user_id' , '=' , 'f.user_id')->where('f.follower_id', '=', $userId);
            })
            ->latest()->paginate(100);

            

                                            
        $posts = PostResource::collection($posts);

        $followings=  UserResource::collection(auth()->user()->followings);
        if ($request->wantsJson()) {
                return $posts;
        }
            return Inertia::render('Home' , ['posts' => $posts , 
            'notifications' => NotificationResource::collection($notifications)
            ,
            'followings' => $followings ] );
        }

        return redirect(route('login'));
    }
}
