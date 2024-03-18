<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function searchUser(Request $request , string $username = null)
    {
        if (!$username)
            return redirect(route('home'));

        $users = User::query()
        ->where('name', 'like', "%$username%")
        ->orWhere('username', 'like', "%$username%")
        ->latest()
        ->get();
        
        return Inertia::render('Search/Users' ,['users' => UserResource::collection($users)]);
    }

}
