<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallInitiated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Conversation $conversation,
        public User $initiatedBy,
        public string $roomName,
    ) {}

    public function broadcastOn(): array
    {
        // Broadcast on each member's personal channel so they receive it
        // regardless of which page they're currently on.
        return $this->conversation->users
            ->reject(fn ($user) => $user->id === $this->initiatedBy->id)
            ->map(fn ($user) => new PrivateChannel('user.'.$user->id))
            ->values()
            ->all();
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversation->id,
            'room_name' => $this->roomName,
            'initiated_by' => [
                'id' => $this->initiatedBy->id,
                'name' => $this->initiatedBy->name,
                'image' => $this->initiatedBy->image,
            ],
        ];
    }
}
