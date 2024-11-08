<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Events\ChatMessageSent;
use App\Services\ChatMessageService;

class ChatMessageController extends Controller
{
    public function destroy(Conversation $conversation,ChatMessage $message)
    {
        abort_if(!isUserContainsInConversation(auth()->user(),$conversation->id),403);

        $message = ChatMessage::where([
            'user_id' => auth()->id(),
            'conversation_id' => $conversation->id,
            'id' => $message->id
        ])->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Deleted Message"
        ]);
    }
    public function store(Request $request,Conversation $conversation,ChatMessageService $chatMessageService)
    {
        abort_if(!isUserContainsInConversation(auth()->user(),$conversation->id),403);

        $message = $chatMessageService->createMessage(auth()->user(),$conversation,$request->message);

        broadcast(new ChatMessageSent($message->load('user:id,name,image')));

        return redirect()->route('conversations.show',$conversation);
        // return response()->json($message);
    }
}
