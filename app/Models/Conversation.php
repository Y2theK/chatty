<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    protected $fillable = [
        'name',
        'is_group'
    ];
    public function allUsers() : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
   
    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class)->whereNot('user_id',auth()->id())->distinct();
    }

    public function messages() : HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function latestMessage() : HasOne
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }
}
