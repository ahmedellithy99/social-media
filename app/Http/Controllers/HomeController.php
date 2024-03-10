<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {   
        // dd(auth()->user()->id));
        if(isset(auth()->user()->id)){

            $userId = auth()->user()->id;
            $posts = Post::with('attachments')->withCount('reactions')
                                            ->withCount('comments')
                                            ->with(['comments','reactions' => function ($query) use ($userId) {
                                                $query->where('user_id', $userId);
                                            }])
                                            ->latest()
                                            ->paginate(20);
    
            return Inertia::render('Home' , ['posts' => PostResource::collection($posts)] );
        }

        return redirect(route('login'));
    }
}
