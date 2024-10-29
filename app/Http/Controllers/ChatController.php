<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Events\SendPublicMessage;

class ChatController extends Controller
{
    public function index()
    {
        $chats = Chat::with('user:id,name')->orderBy('id', 'desc')->take(5)->get()->toArray();
        return Inertia::render('Chat/PublicChat', [
            'chats' => $chats
        ]);

    }
    public function store(Request $request)
    {
        // todo - validate chat request

        $user = auth()->user();

        $chat = $user->chats()->create([
            'message' => $request->message,
        ]);

        $chat->load('user:id,name');

        broadcast(new SendPublicMessage($chat));

        return redirect()->route('chat');

    }
}
