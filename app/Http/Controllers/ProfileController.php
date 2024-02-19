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
                'cover' => 'required|image' ,
                'avatar' => 'nullable'
            ]
            );

        $cover = $data['cover'];
        $avatar = $data['avatar'];

        if($cover)
        {
            $path = $cover->storePublicly('cover/'.$user->id , 'public');
            
            
            if($user->cover_path)
            {   
                Storage::disk('public')->delete($user->cover_path);
            }
            
            $user->update(['cover_path' => $path]);
        }

        return back()->with('status', 'cover-image-update');
    }

    public function index(User $user)
    {
        return Inertia::render('Profile/View' , ['user' => new UserResource($user) , 'status' => session('status') ]);
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
