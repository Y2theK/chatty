<?php

namespace App\Services;

use App\Models\User;
use App\Models\ChatMessage;
use App\Models\Conversation;

class ChatMessageService
{
    public function createMessage(User $user,Conversation $conversation,string $message)
    {

        $chatMessage = ChatMessage::create([
            'user_id' => $user->id,
            'conversation_id' => $conversation->id,
            'message' => $message
        ]);

        return $chatMessage;
    }
}