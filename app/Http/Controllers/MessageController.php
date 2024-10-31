<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function show(User $user)
    {
        return Inertia::render('Chat/PrivateChat',[
            'user' => $user,
            'currentUser' => auth()->user()
        ]);
       
    }

    public function getMessages(User $user)
    {
        return Message::query()
            ->where(function ($query) use ($user) {
                $query->where('sender_id', auth()->id())
                    ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', auth()->id());
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();
    }


    public function sendMessage(Request $request, User $user)
    {
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'text' => $request->input('message')
        ]);

        broadcast(new MessageSent($message));

        return response()->json($message);
    }
}
