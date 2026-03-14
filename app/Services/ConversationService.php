<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ConversationService
{
    public function getuserConversation(User $user): Collection
    {
        $conversations = $user->conversations()->with(['users:id,name,last_active_at,image', 'latestMessage'])->get();

        return $conversations;
    }
}
