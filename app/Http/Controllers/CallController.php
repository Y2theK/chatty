<?php

namespace App\Http\Controllers;

use App\Events\CallEnded;
use App\Events\CallInitiated;
use App\Events\ChatMessageSent;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Services\ChatMessageService;
use App\Services\LiveKitService;
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function __construct(
        protected LiveKitService $liveKitService,
        protected ChatMessageService $chatMessageService,
    ) {}

    public function start(Conversation $conversation)
    {
        abort_if(! isUserContainsInConversation(auth()->user(), $conversation->id), 403);

        $user = auth()->user();
        $roomName = $this->liveKitService->getRoomName($conversation->id);
        $token = $this->liveKitService->generateToken($roomName, $user->id, $user->name);

        broadcast(new CallInitiated($conversation->load('users'), $user, $roomName));

        return response()->json([
            'token' => $token,
            'room_name' => $roomName,
            'livekit_url' => config('services.livekit.url'),
        ]);
    }

    public function join(Request $request, Conversation $conversation)
    {
        abort_if(! isUserContainsInConversation(auth()->user(), $conversation->id), 403);

        $user = auth()->user();
        $roomName = $request->string('room_name');
        $token = $this->liveKitService->generateToken($roomName, $user->id, $user->name);

        return response()->json([
            'token' => $token,
            'room_name' => $roomName,
            'livekit_url' => config('services.livekit.url'),
        ]);
    }

    public function end(Conversation $conversation)
    {
        abort_if(! isUserContainsInConversation(auth()->user(), $conversation->id), 403);

        $user = auth()->user();

        $message = ChatMessage::create([
            'user_id' => $user->id,
            'conversation_id' => $conversation->id,
            'message' => 'Video call ended',
            'type' => 'system',
        ]);

        broadcast(new ChatMessageSent($this->chatMessageService->loadMessageRelationData($message)));
        broadcast(new CallEnded($conversation, $user));

        return response()->json(['success' => true]);
    }
}
