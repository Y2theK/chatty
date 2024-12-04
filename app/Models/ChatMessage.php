<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function reply(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'chat_message_id', 'id');
    }

    public function upload(): BelongsTo
    {
        return $this->belongsTo(Upload::class);
    }
}
