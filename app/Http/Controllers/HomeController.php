<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
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
            $posts = Post::with('attachments')->withCount('reactions')
                                            ->withCount('comments')
                                            ->with(['comments' => function($query) use ($userId) {
                                                $query->withCount('reactions')->with(
                                                    ['reactions' => function ($query) use ($userId) {
                                                        $query->where('user_id', $userId);
                                                    }
                                            ]);
                                            }
                                            ,'reactions' => function ($query) use ($userId) {
                                                $query->where('user_id', $userId);
                                            }])
                                            ->latest()
                                            ->paginate(5);


                                            
        $posts = PostResource::collection($posts);
        
        if ($request->wantsJson()) {
                return $posts;
        }
            return Inertia::render('Home' , ['posts' => $posts , 'notifications' => $notifications ] );
        }

        return redirect(route('login'));
    }
}
