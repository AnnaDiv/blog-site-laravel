<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blocks extends Model
{
    use HasFactory;

    protected $table = 'blocks';

    protected $fillable = [
        'user_nickname',
        'blockingUser'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_nickname', 'nickname');
    }

    public function blockingUserModel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blockingUser', 'nickname');
    }
}
