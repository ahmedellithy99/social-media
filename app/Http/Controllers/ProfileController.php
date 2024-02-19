<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
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

    public function index(User $user)
    {
        return Inertia::render('Profile/View' , ['user' => new UserResource($user) , 'success' => session('success'), ]);
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
