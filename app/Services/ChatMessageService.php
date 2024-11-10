<?php

namespace App\Services;

use App\Models\User;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Message;

class ChatMessageService
{
    public function createMessage(User $user,Conversation $conversation,string $message) : ChatMessage
    {
        $chatMessage = ChatMessage::create([
            'user_id' => $user->id,
            'conversation_id' => $conversation->id,
            'message' => $message,
        ]);

        return $chatMessage;
    }
    public function addSeenByUser(ChatMessage $message,User $user) : ChatMessage
    {
        $seen_by = $message->seen_by;
        $seen_by_array = explode(',',$seen_by);
        if(!in_array($user->id,$seen_by_array) && $message->user_id != $user->id){
            tap($message->update([
                'seen_by' => $seen_by ? $seen_by . ',' . $user->id : $user->id
            ]));
        }
       
        return $message;
    }
}