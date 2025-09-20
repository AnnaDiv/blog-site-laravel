<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;

class Profile extends Model
{
    protected $table = 'profile';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'followers_count',
        'following_count'
    ];

    /*
     * Get the user that owns the Profile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
