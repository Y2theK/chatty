<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationUser extends Model
{
    protected $table = 'conversation_user';

    protected $guarded = [];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ConversationUser::class);
    }
}
