<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index(Request $request , Chat $id)
    {
        $chat =Chat::with('messages')->get();

        return Inertia::render('Chat/Chat', ['chat' => $chat] );
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

        // return back();   
    }
}
