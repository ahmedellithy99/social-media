<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Follower;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function updateImage(Request $request)
    {
        $user = $request->user();
        

        $data = $request->validate(
            [
                'cover' => 'nullable|image' ,
                'avatar' => 'nullable|image'
            ]
            );

        $cover = $data['cover'] ?? null ;
        $avatar = $data['avatar'] ?? null;

        $success = '';

        if($cover)
        {
            $path = $cover->storePublicly('user/'.$user->id , 'public');
            
            
            if($user->cover_path)
            {   
                Storage::disk('public')->delete($user->cover_path);
            }
            
            $user->update(['cover_path' => $path]);
            
            $success = 'Your cover image was updated';
        }

        if ($avatar) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $path = $avatar->store('user/'.$user->id, 'public');
            $user->update(['avatar_path' => $path]);
            $success = 'Your avatar image was updated';
        }

        return back()->with('success', $success);
    }

    public function index(Request $request, User $user)
    {   
        $isFollowing = false;
        if(Auth::guest())
        {
            return redirect(route('login'));
        }
        if(auth()->user()->id != $user->id)
        {
            
            $isFollowing = Follower::where('user_id' , $user->id)->where('follower_id' , auth()->user()->id)->exists();
        }

        $posts = Post::items(auth()->user()->id)->latest()->paginate(10);
        
        if ($request->wantsJson()) {
            return PostResource::collection($posts);
        }

        $followers = $user->followers;
        

        $followings = $user->followings;

        // return $followers;
        $followersCount = count($user->followers);
        $followingsCount = count($user->followings);

        
        return Inertia::render('Profile/View' , 
        ['user' => new UserResource($user) ,
        'success' => session('success'),
        'followers' => UserResource::collection($followers),
        'followings' => UserResource::collection($followings),
        'followersCount' => $followersCount,
        'followingsCount' => $followingsCount,
        'isFollowing' => $isFollowing,
        'posts' => PostResource::collection($posts)
        
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $userReq = $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile' ,['user' => $userReq->username ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
