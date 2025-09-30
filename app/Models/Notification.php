<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';

    public $timestamps = false;

    protected $fillable = [
        'notification_owner_id',
        'sender_id',
        'content',
        'place',
        'link',
        'used',
        'expires_at'
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class, 'notification_owner_id', 'id');
    }

    public function sender() : BelongsTo {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

}
