<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {   

        if(Auth::authenticate()){
            $authUser = auth()->user();
            $authId = $authUser->id;

            // Sorted  Chats 
            $chats = ChatResource::collection(Chat::with('lastMessage')->where('A' , $authId )->orWhere('B' , $authId)->get());
            $chats = $chats->toArray(request());
            usort($chats, function($a, $b) {
                return   strtotime($b['timeOflastMessage']) - strtotime($a['timeOflastMessage']);
            });
            // Notifications 

            $notifications = $authUser->notifications;
            // Unread Notifications Count
            $countUnReads = $authUser->unReadNotifications->count();

            // Posts 
            $posts = Post::items($authId)
                ->join('followers AS f' , function ($join) use ($authId) {
                $join->on('posts.user_id' , '=' , 'f.user_id')->where('f.follower_id', '=', $authId);
            })
            ->latest()->paginate(10);
        
            $posts = PostResource::collection($posts);
        
            //Followings 
            $followings=  UserResource::collection(auth()->user()->followings);

            // Paginating Request 
            if ($request->wantsJson()) {
                    return $posts;
            }

                return Inertia::render('Home' , ['posts' => $posts , 
                'notifications' => NotificationResource::collection($notifications),
                'countUnReads' => $countUnReads,
                'followings' => $followings ,
                'chats' => $chats] );
            }

            return redirect(route('login'));
    }

    
}
