<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Models\ConversationUser;
use App\Services\ConversationService;

class DashboardController extends Controller
{
    // public function index(ConversationService $conversationService)
    // {

    //     $user = auth()->user();
    //     $conversations = $conversationService->getuserConversation($user);

    //     return Inertia::render('Chat/EmptyChat',[
    //         'conversations' => $conversations,
    //     ]);

    // }
    
}
