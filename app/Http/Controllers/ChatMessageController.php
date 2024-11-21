<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Events\ChatMessageSent;
use App\Events\ConversationUpdate;
use App\Services\ChatMessageService;
use App\Http\Requests\MessageCreateRequest;
use Illuminate\Broadcasting\PrivateChannel;

class ChatMessageController extends Controller
{
    public function __construct(protected ChatMessageService $chatMessageService)
    {
        
    }
    public function store(MessageCreateRequest $request,Conversation $conversation)
    {

        abort_if(!isUserContainsInConversation(auth()->user(),$conversation->id),403);

        $message = $this->chatMessageService->createMessage(auth()->user(),$conversation,$request->validated());

        $conversation->update(['updated_at' => now()]);
        
        // chat message sending in real time
        broadcast(new ChatMessageSent($this->chatMessageService->loadMessageRelationData($message)));
        
        // conversation updating in real time , to move to the top of coversations lists in sidebars
        broadcast(new ConversationUpdate($conversation->load(['users:id,name,last_active_at,image','latestMessage'])))->toOthers(); 

        return redirect()->route('conversations.show',$conversation);
    }

    public function destroy(Conversation $conversation,ChatMessage $message)
    {
        abort_if(!isUserContainsInConversation(auth()->user(),$conversation->id),403);

       $this->chatMessageService->deleteMessage($conversation,$message);
        
        return response()->json([
            'success' => true,
            'message' => "Deleted Message"
        ]);
    }
    public function addSeenBy(Conversation $conversation)
    {
        abort_if(!isUserContainsInConversation(auth()->user(),$conversation->id),403);

        $user = auth()->user();
        //update seen by to latestMessage if user enter to the conversation
        if($conversation->latestMessage){
            $this->chatMessageService->addSeenByUser($conversation->latestMessage,$user);
        }

        // to indicate (font bold) if the conversation latestMessage is not seen
        broadcast(new ConversationUpdate($conversation->load(['users:id,name,last_active_at,image','latestMessage'])))->toOthers(); 

        return response()->json([
            'success' => true,
            'message' => "Added Seenby",
            'data' =>$this->chatMessageService->loadMessageRelationData($conversation->latestMessage)
        ]);
    }
  
}
