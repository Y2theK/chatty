<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Events\ChatMessageSent;
use App\Models\ConversationUser;
use App\Services\ConversationService;
use App\Http\Resources\MessageResource;

class ConversationController extends Controller
{
    public function __construct(protected ConversationService $conversationService)
    {
        
    }

    
   
    public function index()
    {

        $user = auth()->user();
        $conversations = $this->conversationService->getuserConversation($user);
        return Inertia::render('Chat/EmptyChat',[
            'conversations' => $conversations,
        ]);

    }
    public function show(Conversation $conversation)
    {
        $user = auth()->user();
        $conversations = $this->conversationService->getuserConversation($user);
      
        $messages = ChatMessage::with('user:id,name')->where('conversation_id',$conversation->id)->orderBy('created_at','desc')->paginate(50);
        // dd($messages,MessageResource::collection($messages));
        return Inertia::render('Chat/Chat',[
            'conversations' => $conversations,
            'messages' => $messages,
            'conversation' => $conversation->load('users:id,name,last_active_at')
        ]);
    }

    public function deleteMessage(Conversation $conversation,ChatMessage $message)
    {
        $message = ChatMessage::where([
            'user_id' => auth()->id(),
            'conversation_id' => $conversation->id,
            'id' => $message->id
        ])->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Deleted Message"
        ]);
    }
    public function store(Request $request,Conversation $conversation)
    {
        $message = $this->createMessage(auth()->user(),$conversation,$request->message);

        broadcast(new ChatMessageSent($message->load('user:id,name')));

        return redirect()->route('conversations.show',$conversation);
        // return response()->json($message);
    }

    public function updateLastActiveAt(Request $request)
    {

        auth()->user()->update([
            'last_active_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Updated Last Active At."
        ]);
    }

    protected function createMessage(User $user,Conversation $conversation,string $message)
    {

        $chatMessage = ChatMessage::create([
            'user_id' => $user->id,
            'conversation_id' => $conversation->id,
            'message' => $message
        ]);

        return $chatMessage;
    }

    protected function createConversationUser(User $user,Conversation $conversation)
    {
        $conversationUser = ConversationUser::create([
            'user_id' => $user->id,
            'conversation_id' => $conversation->id
        ]);

        return $conversationUser;
    }

    public function createConversation(Request $request)
    {
        $user = User::where('email',$request->email)->first();

        if($user && $user == auth()->user()){
            return response()->json([
                'success' => true,
                'redirect' => route('dashboard')
            ]);
        }

        $authUserConversation = auth()->user()->conversations->where('is_group',false)->pluck('id')->toArray();

        $userConversation = $user->conversations->where('is_group',false)->pluck('id')->toArray();

        $isAlreadyHaveConversation = array_values(array_intersect($authUserConversation,$userConversation));

        if($isAlreadyHaveConversation){

            $alreadyConversation = Conversation::where('id',$isAlreadyHaveConversation[0])->first();

            if($request->message){
               $this->createMessage(auth()->user(),$alreadyConversation,$request->message);
            }

                // boradcase noti
            return response()->json([
                'success' => true,
                'redirect' => route('conversations.show',$alreadyConversation)
            ]);
        }
                                                        
        $conversation = Conversation::create([
            'name' => fake()->emoji()
        ]);

        $this->createConversationUser($user,$conversation);
        $this->createConversationUser(auth()->user(),$conversation);

        if($request->message){
            $this->createMessage(auth()->user(),$conversation,$request->message);
        }
        

        // boradcase noti

        return response()->json([
            'success' => true,
            'redirect' => route('conversations.show',$conversation)
        ]);    

    }

    public function addGroup(Conversation $conversation,Request $request)
    {
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

            ConversationUser::where('user_id',auth()->id())->where('conversation_id',$conversation->id)->delete();
           
            // boradcase noti
            return response()->json([
                'success' => true,
                'redirect' => route('dashboard')
            ]);      
    }
   
}
