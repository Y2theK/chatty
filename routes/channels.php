<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('publicChat', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{id}', function ($user, $id) {
    // dd($user->id,$id);
    return (int) $user->id === (int) $id;
});

Broadcast::channel('conversation.{id}', function ($user, $id) {
    $conversation = Conversation::where('id',$id)->first();
    return $conversation->allUsers()->pluck('users.id')->contains($user->id);
});

Broadcast::channel('presence.chat', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});