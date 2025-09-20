<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'user_id',
        'content',
        'time'
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function post() : BelongsTo {
        return $this->belongsTo(Post::class);
    }
}
