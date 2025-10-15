<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;
use App\Models\Message;

class Conversation extends Model
{
    protected $table = 'conversations';

    public function users() : BelongsToMany {
        return $this->belongsToMany(User::class);         
    }

    public function messages() : HasMany {
        return $this->hasMany(Message::class);        
    }
}
