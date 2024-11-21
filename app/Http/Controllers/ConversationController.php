<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Models\ConversationUser;
use App\Services\ChatMessageService;
use App\Services\ConversationService;
use App\Http\Requests\ConversationCreateRequest;

class ConversationController extends Controller
{
    public function __construct(protected ConversationService $conversationService){}

    public function index()
    {

        $user = auth()->user();
        $conversations = $this->conversationService->getuserConversation($user);
        return Inertia::render('Chat/EmptyChat',[
            'conversations' => $conversations,
        ]);

    }
    public function show(Conversation $conversation,ChatMessageService $chatMessageService)
    {
        abort_if(!isUserContainsInConversation(auth()->user(),$conversation->id),403);

        $user = auth()->user();
        //update seen by to latestMessage if user enter to the conversation
        if($conversation->latestMessage){
            $chatMessageService->addSeenByUser($conversation->latestMessage,$user);
        }

        $conversations = $this->conversationService->getuserConversation($user);      
        $messages = $chatMessageService->getMessages($conversation);
        // dd($messages);
        return Inertia::render('Chat/Chat',[
            'conversations' => $conversations,
            'messages' => $messages,
            'conversation' => $conversation->load(['users:id,name,last_active_at,image','latestMessage'])
        ]);
    }

    public function createConversation(ConversationCreateRequest $request,ChatMessageService $chatMessageService)
    {
        $user = User::where('email',$request->email)->first();

        if($user && $user == auth()->user()){
            return response()->json([
                'success' => true,
                'redirect' => route('dashboard')
            ]);
        }

        // check auth user and emailed user have already have conversation or not
        $authUserConversation = auth()->user()->conversations->where('is_group',false)->pluck('id')->toArray();

        $userConversation = $user->conversations->where('is_group',false)->pluck('id')->toArray();

        $isAlreadyHaveConversation = array_values(array_intersect($authUserConversation,$userConversation));

        if($isAlreadyHaveConversation){
            $conversation = Conversation::where('id',$isAlreadyHaveConversation[0])->first();
        }else{

            //if new conversation
            $conversation = Conversation::create([
                'name' => fake()->emoji()
            ]);
    
            $this->createConversationUser($user,$conversation);
            $this->createConversationUser(auth()->user(),$conversation);
        }
                                                        
        if($request->message){
            $chatMessageService->createMessage(auth()->user(),$conversation,$request->validated());
        }
        // boradcase noti

        return response()->json([
            'success' => true,
            'redirect' => route('conversations.show',$conversation)
        ]);    

    }

    public function addGroup(Conversation $conversation,ConversationCreateRequest $request)
    {
        abort_if(!isUserContainsInConversation(auth()->user(),$conversation->id),403);

        $user = User::where('email',$request->email)->first();

        if(!$user){
            return response()->json([
                'success' => true,
                'redirect' => route('dashboard')
            ]);
        }

        $conversation->update([
            'is_group' => true,
            'name' => $conversation->name ?? fake()->emoji()
        ]);

        $this->createConversationUser($user,$conversation);

        // boradcase noti

        return response()->json([
            'success' => true,
            'redirect' => route('conversations.show',$conversation)
        ]);
      
    }
    public function leaveConversation(Conversation $conversation,Request $request)
    {

        abort_if(!isUserContainsInConversation(auth()->user(),$conversation->id),403);

        ConversationUser::where('user_id',auth()->id())->where('conversation_id',$conversation->id)->delete();
        
        // boradcase noti
        return response()->json([
            'success' => true,
            'redirect' => route('dashboard')
        ]);      
    }

    protected function createConversationUser(User $user,Conversation $conversation)
    {
        $conversationUser = ConversationUser::create([
            'user_id' => $user->id,
            'conversation_id' => $conversation->id
        ]);

        return $conversationUser;
    }
   
}
