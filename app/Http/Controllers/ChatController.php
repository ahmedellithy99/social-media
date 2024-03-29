<?php

namespace App\Http\Controllers;

use App\Events\SendMessage;
use App\Http\Resources\ChatResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index(Request $request, Chat $chat)
    {
        $this->authorize('viewAny' , $chat);
        $authUser = auth()->user();
        $authId = $authUser->id;
        // retrieving the Current Chat 
        $chat = $chat->load(['messages', 'UserA', 'UserB']);
        //Sender And Recipient
        $sender = ($authId == $chat['A']) ? $chat['UserA'] : $chat['UserB'];
        $recipient = ($authId == $chat['B']) ? $chat['UserA'] : $chat['UserB'];

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
        

        return Inertia::render('Chat/Chat', [
            'chat' => $chat,
            'recipient' => new UserResource($recipient),
            'sender' => new UserResource($sender),
            'notifications' => NotificationResource::collection($notifications),
            'chats' => $chats,
            'countUnReads' => $countUnReads,
            'countUnReadChats' => $countUnReadChats
        ]);
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'body' => 'required',
            'chat_id' => 'exists:chats,id',
        ]);
        $chat = Chat::findOrFail($data['chat_id']);
        
        $this->authorize('create' , $chat );

        $message = Message::create([
            'sender_id' => auth()->user()->id,
            'body' => $data['body'],
            'chat_id' => $data['chat_id']
        ]);

        // BroadcastHere 
        broadcast(new SendMessage($data['chat_id'], $message));

        return back();
    }

    public function markAsRead(Message $msg)
    {
        $msg->update(['read' => true]);
    }
}
