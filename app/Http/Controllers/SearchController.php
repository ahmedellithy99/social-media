<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function userSearch(Request $request)
    {
        $users = User::query()
        ->when(request('search') , function($query , $search)
        {
            $query->where('name', 'like', "%$search%")
            ->orWhere('username', 'like', "%$search%");
        }
        )
        ->latest()
        ->paginate(5);

        return Inertia::render('Search/Users' ,['users' => UserResource::collection($users) 
        , 'filter' => request('search') ]);
    }


}
