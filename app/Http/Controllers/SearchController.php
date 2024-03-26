<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function userSearch(Request $request)
    {
        $authUser = auth()->user();
        $authId = $authUser->id;

        $users = User::query()
            ->when(
                request('search'),
                function ($query, $search) {
                    $query->where('name', 'like', "%$search%")
                        ->orWhere('username', 'like', "%$search%");
                }
            )
            ->latest()
            ->paginate(5);

        //Notifications for The Authenticated Layout
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

        return Inertia::render(
            'Search/Users',
            [
                'users' => UserResource::collection($users), 'filter' => request('search'), 'notifications' => NotificationResource::collection($notifications),
                'chats' => $chats,
                'countUnReads' => $countUnReads,
                'countUnReadChats' => $countUnReadChats
            ],
        );
    }
}
