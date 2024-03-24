<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function userSearch(Request $request)
    {   
        $authId = auth()->user()->id ;
        $users = User::query()
        ->when(request('search') , function($query , $search)
        {
            $query->where('name', 'like', "%$search%")
            ->orWhere('username', 'like', "%$search%");
        }
        )
        ->latest()
        ->paginate(5);

        //Notifications for The Authenticated Layout
        $notifications = auth()->user()->unReadNotifications;

         // Sorted Chats for The Authenticated Layout
        $chats = ChatResource::collection(Chat::with('lastMessage')->where('A' , $authId )->orWhere('B' , $authId)->get());
        $chats = $chats->toArray(request());
        usort($chats, function($a, $b) {
            return   strtotime($b['timeOflastMessage']) - strtotime($a['timeOflastMessage']);
        });

        return Inertia::render('Search/Users' ,['users' => UserResource::collection($users) 
        , 'filter' => request('search')
        ,'notifications' => $notifications,
        'chats' => $chats
 ],
    );
    }


}
