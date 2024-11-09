<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


// Broadcast::channel('publicChat', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// Broadcast::channel('chat.{id}', function ($user, $id) {
//     // dd($user->id,$id);
//     return (int) $user->id === (int) $id;
// });

// Broadcast::channel('presence.chat', function ($user) {
//     return ['id' => $user->id, 'name' => $user->name];
// });

// Broadcast::channel('conversation.{id}', function ($user, $id) {
//     return isUserContainsInConversation($user,$id);
// });

Broadcast::channel('conversation.{id}', function ($user,$id) {
    $isCorrectUser = isUserContainsInConversation($user,$id);
    return $isCorrectUser ? ['id' => $user->id, 'name' => $user->name] : [];
});


Broadcast::channel('online', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});

