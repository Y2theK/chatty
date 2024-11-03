<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'conversation' => $conversation->load('users:id,name')
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

    public function createConversation(Request $request)
    {
        $user = User::where('email',$request->email)->first();;

        if($user && $user !== auth()->user()){

            $conversation = Conversation::create([
                'name' => ''
            ]);

            ConversationUser::firstOrCreate([
                'user_id' => $user->id,
                'conversation_id' => $conversation->id
            ]);

            ConversationUser::firstOrCreate([
                'user_id' => auth()->id(),
                'conversation_id' => $conversation->id
            ]);

            ChatMessage::create([
                'user_id' => auth()->id(),
                'conversation_id' => $conversation->id,
                'message' => $request->message
            ]);
            // boradcase noti

            return response()->json([
                'success' => true,
                'redirect' => route('conversations.show',$conversation)
            ]);
        }

        return response()->json([
            'success' => true,
            'redirect' => route('dashboard')
        ]);
    }

    public function addGroup(Conversation $conversation,Request $request)
    {
        $user = User::where('email',$request->email)->first();;

        if($user){
            ConversationUser::firstOrCreate([
                'user_id' => $user->id,
                'conversation_id' => $conversation->id
            ]);
            // boradcase noti
            return response()->json([
                'success' => true,
                'redirect' => route('conversations.show',$conversation)
            ]);
        }
        return response()->json([
            'success' => true,
            'redirect' => route('dashboard')
        ]);
      
    }
   
}
