<?php

use App\Models\Conversation;
use App\Models\User;

function isUserContainsInConversation(User $user,int $id) : bool
{
    $conversation = Conversation::where('id',$id)->first();
    $isCorrectUser = $conversation->users()->pluck('users.id')->contains($user->id);

    return $isCorrectUser;
}