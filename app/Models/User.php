<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nickname',
        'motto',
        'email',
        'password',
        'image_folder',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function blockedUsers(): BelongsToMany
    {
        // this user (by nickname) -> blocks rows where blocks.user_nickname = users.nickname
        // returns User models of those blocked (joined via blocks.blockingUser -> users.nickname)
        return $this->belongsToMany(
            User::class,     // related model
            'blocks',        // pivot table
            'user_nickname', // pivot column pointing to this user's nickname
            'blockingUser',  // pivot column pointing to the other user's nickname
            'nickname',      // local key on this model
            'nickname'  // related key on related model
        );
    }

    public function blockedBy()
    {
        return $this->belongsToMany(
            User::class,
            'blocks',
            'blockingUser',  // pivot column that points to the "blocker"
            'user_nickname', // pivot column that points to the "blocked"
            'nickname',
            'nickname'
        );
    }
}
