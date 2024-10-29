<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    protected $fillable = [
        'message',
        'room_id',
    ];

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
