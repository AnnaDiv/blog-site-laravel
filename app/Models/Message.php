<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;
use App\Models\Conversation;

class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'read'
    ];

    public function conversation() : BelongsTo {
        return $this->BelongsTo(Conversation::class);    
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
