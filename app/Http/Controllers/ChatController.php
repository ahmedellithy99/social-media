<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index(Request $request , Chat $chat)
    {   
        $chat = $chat->load(['messages' , 'UserA' , 'UserB']);
        
        if(auth()->user()->id == $chat['A'])
        {
            $sender = $chat['UserA'];
            $recipient = $chat['UserB'];
        }
        elseif(auth()->user()->id == $chat['B']){
            $sender = $chat['UserB'];
            $recipient = $chat['UserA'];
        }
        else
        {
            return response('You are not authorized' , 403);
        }

        return Inertia::render('Chat/Chat', ['chat' => $chat , 
        'recipient' => new UserResource($recipient), 'sender' => new UserResource($sender)] );
    }
    
    public function store(Request $request)
    {
        $data =$request->validate([
            'body' => 'required' ,
            'chat_id' => 'exists:chats,id'
        ]);
        // return $data['chat_id'];
        Message::create([
            'sender_id' => auth()->user()->id,
            'body' => $data['body'],
            'chat_id' => $data['chat_id']
        ]);

        return back();   
    }
}
