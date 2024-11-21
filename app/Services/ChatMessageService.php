<?php

namespace App\Services;

use App\Models\User;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Services\FileUploadService;
use GuzzleHttp\Psr7\Message;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatMessageService
{
    public function getMessages(Conversation $conversation) : LengthAwarePaginator
    {
        return ChatMessage::with(['user:id,name,image','reply','upload:id,file_name,file_original_name,type'])
                                        ->where('conversation_id',$conversation->id)
                                        ->orderBy('created_at','desc')
                                        ->paginate(50);
    }

    public function loadMessageRelationData(ChatMessage $message) : ChatMessage
    {
        return $message->load(['user:id,name,last_active_at,image','reply','upload:id,file_name,file_original_name,type']);
    }

    public function createMessage(User $user,Conversation $conversation,array $data) : ChatMessage
    {
        $uploadId = null;
        if($data['file']){
            $path = 'conversations/';
            $imageService = new FileUploadService;
            $uploadId = $imageService->upload($data['file'],$path,'public');
        }

        $chatMessage = ChatMessage::create([
            'user_id' => $user->id,
            'conversation_id' => $conversation->id,
            'message' => $data['message'],
            'chat_message_id' => $data['replyMessageId'],
            'upload_id' => $uploadId

        ]);

        return $chatMessage;
    }
    public function deleteMessage(Conversation $conversation,ChatMessage $message)
    {
        $imageService = new FileUploadService;

        $imageService->delete($message->upload,'public');

        $message = ChatMessage::where([
            'user_id' => auth()->id(),
            'conversation_id' => $conversation->id,
            'id' => $message->id
        ])->delete();

        return $message;
    }
    public function addSeenByUser(ChatMessage $message,User $user) : ChatMessage
    {
        $seen_by = $message->seen_by;
        $seen_by_array = explode(',',$seen_by);
        if(!in_array($user->id,$seen_by_array) && $message->user_id != $user->id){
            tap($message->update([
                'seen_by' => $seen_by ? $seen_by . ',' . $user->id : $user->id
            ]));
        }
       
        return $message;
    }
}