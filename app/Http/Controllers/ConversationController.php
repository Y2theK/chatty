<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Message;
use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Services\ConversationService;

class ConversationController extends Controller
{
    public function __construct(protected ConversationService $conversationService)
    {
        
    }
    public function index()
    {

        $user = auth()->user();
        $conversations = $this->conversationService->getuserConversation($user);

        return Inertia::render('Chat/EmptyChat',[
            'conversations' => $conversations,
        ]);

    }
    public function show(Conversation $conversation)
    {
        $user = auth()->user();
        $conversations = $this->conversationService->getuserConversation($user);

        $messages = ChatMessage::with('user:id,name')->where('conversation_id',$conversation)->orderBy('created_at','desc')->get();

        return Inertia::render('Chat/Chat',[
            'conversations' => $conversations,
            'messages' => $messages,
        ]);
    }

   
}
