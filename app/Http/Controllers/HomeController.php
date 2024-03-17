<?php

namespace App\Http\Controllers;

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
            
            $notifications = auth()->user()->unReadNotifications;
            $userId = auth()->user()->id;
            
            $posts = Post::items($userId)->join('followers AS f' , function ($join) {
                $join->on('posts.user_id' , '=' , 'f.user_id')->where('f.follower_id', '=', 2);
            })
            ->latest()->paginate(5);

            

                                            
        $posts = PostResource::collection($posts);

        $followings=  UserResource::collection(auth()->user()->followings);
        if ($request->wantsJson()) {
                return $posts;
        }
            return Inertia::render('Home' , ['posts' => $posts , 
            'notifications' => $notifications ,
            'followings' => $followings ] );
        }

        return redirect(route('login'));
    }
}
