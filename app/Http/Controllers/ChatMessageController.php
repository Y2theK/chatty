<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Events\ChatMessageSent;
use App\Events\ConversationUpdate;
use App\Services\ChatMessageService;
use Illuminate\Broadcasting\PrivateChannel;

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
        $conversation->update(['updated_at' => now()]);
        
        // chat message sending in real time
        broadcast(new ChatMessageSent($message->load('user:id,name,image')))->toOthers(); 
        
        // conversation updating in real time , to move to the top of coversations lists in sidebars
        broadcast(new ConversationUpdate($conversation->load(['users:id,name,last_active_at,image','latestMessage'])))->toOthers(); 
        return redirect()->route('conversations.show',$conversation);
        // return response()->json($message);
    }
}
