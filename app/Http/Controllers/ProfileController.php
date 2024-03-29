<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ChatResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Chat;
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
    public function updateImage(Request $request , User $user)
    {
        $this->authorize('updateImage' , $user);
        
        $authUser = $request->user();
        $data = $request->validate(
            [
                'cover' => 'nullable|image',
                'avatar' => 'nullable|image'
            ]
        );
        
        $cover = $data['cover'] ?? null;
        $avatar = $data['avatar'] ?? null;

        $success = '';

        if ($cover) {
            $path = $cover->storePublicly('user/' . $authUser->id, 'public');


            if ($authUser->cover_path) {
                Storage::disk('public')->delete($authUser->cover_path);
            }

            $authUser->update(['cover_path' => $path]);

            $success = 'Your cover image was updated';
        }

        if ($avatar) {
            if ($authUser->avatar_path) {
                Storage::disk('public')->delete($authUser->avatar_path);
            }
            $path = $avatar->store('user/' . $authUser->id, 'public');
            $authUser->update(['avatar_path' => $path]);
            $success = 'Your avatar image was updated';
        }

        return back()->with('success', $success);
    }

    public function index(Request $request, User $user)
    {   
        $authUser = auth()->user();
        $authId = $authUser->id;

        $isFollowing = false;
        // Authenticated Users Only 
        if (auth()->user()->id != $user->id) {

            $isFollowing = Follower::where('user_id', $user->id)->where('follower_id', $authId)->exists();
        }
        //Notifications
        $notifications = $authUser->notifications;
        $countUnReads = $authUser->unReadNotifications->count();

       // Sorted Chats for The Authenticated Layout
        $chats = ChatResource::collection(Chat::with('lastMessage')->where('A', $authId)->orWhere('B', $authId)->get());
        $chats = $chats->toArray(request());
        usort($chats, function ($a, $b) {
            return   strtotime($b['timeOflastMessage']) - strtotime($a['timeOflastMessage']);
        });
        
        //Counting UnRead Chats 
        $countUnReadChats = 0 ; 
        foreach($chats as $chata)
        {
            if($chata['lastMessage'] == null || $chata['lastMessage']['sender_id'] == $authId )
            {
                continue;
            }
            if($chata['lastMessage']['read'] == 0)
            {
                $countUnReadChats += 1 ;
            }
        }

        // Posts and filtering the posts 
        $filter = request('search');
        $posts = Post::items($authId)
            ->when(request('search'), function ($query, $search) {
                $query->where('body', 'like', "%$search%");
            })
            ->where('user_id', $user->id)
            ->latest()->paginate(10);

        // Paginations Magic 
        if ($request->wantsJson()) {
            return PostResource::collection($posts);
        }

        // Followers and Followings
        $followers = $user->followers;
        $followings = $user->followings;
        $followersCount = count($user->followers);
        $followingsCount = count($user->followings);


        return Inertia::render(
            'Profile/View',
            [
                'user' => new UserResource($user),
                'success' => session('success'),
                'followers' => UserResource::collection($followers),
                'followings' => UserResource::collection($followings),
                'followersCount' => $followersCount,
                'followingsCount' => $followingsCount,
                'isFollowing' => $isFollowing,
                'posts' => PostResource::collection($posts),
                'filter' => $filter,
                'notifications' => NotificationResource::collection($notifications),
                'chats' => $chats,
                'countUnReads' => $countUnReads,
                'countUnReadChats' => $countUnReadChats

            ]
        );
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, User $user): RedirectResponse
    {   
        $this->authorize('update' , $user);
        $userReq = $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile', ['user' => $userReq->username]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request , User $user): RedirectResponse
    {
        $this->authorize('delete' , $user);
        
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
