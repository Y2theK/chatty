<?php

namespace App\Services;

use App\Models\User;

class ConversationService
{
    public function getuserConversation(User $user) 
    {
        $conversations = $user->conversations()->with('users:id,name,email')->get();
        return $conversations;
    }
}