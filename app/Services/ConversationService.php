<?php

namespace App\Services;

use App\Models\User;
use App\Models\Conversation;

class ConversationService {

    public function between(User $userA, User $userB) : Conversation {

        $conversation = Conversation::whereHas('users', fn($q) => $q->where('users.id', $userA->id))
            ->whereHas('users', fn($q) => $q->where('users.id', $userB->id))
            ->whereRaw('(select count(*) from conversation_user where conversation_id = conversations.id) = 2')
            ->first();

        if (!$conversation){
            $conversation = Conversation::create();
            $conversation->users()->attach([$userA->id, $userB->id]);
        }
        return $conversation;
    }

}