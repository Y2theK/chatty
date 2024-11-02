<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Events\ChatMessageSent;
use App\Models\ConversationUser;
use App\Services\ConversationService;
use App\Http\Resources\MessageResource;

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
      
        $messages = ChatMessage::with('user:id,name')->where('conversation_id',$conversation->id)->orderBy('created_at','desc')->paginate(50);
        // dd($messages,MessageResource::collection($messages));
        return Inertia::render('Chat/Chat',[
            'conversations' => $conversations,
            'messages' => $messages,
            'conversation' => $conversation
        ]);
    }

    public function store(Request $request,Conversation $conversation)
    {
        $message = ChatMessage::create([
            'user_id' => auth()->id(),
            'conversation_id' => $conversation->id,
            'message' => $request->input('message')
        ]);

        broadcast(new ChatMessageSent($message->load('user:id,name')));

        return redirect()->route('conversations.show',$conversation);
        // return response()->json($message);
    }
   
}
