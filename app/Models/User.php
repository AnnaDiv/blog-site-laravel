<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Like;

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

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_nickname', 'nickname');
    }

    public function blockedUsers(): BelongsToMany
    {
        // blocked users by this user
        return $this->belongsToMany(
            User::class,
            'blocks',
            'blockingUser',   // this users nickname
            'user_nickname',  // blocked users nickname
            'nickname',
            'nickname'
        );
    }

    public function blockedBy(): BelongsToMany
    {
        // blocked by other users 
        return $this->belongsToMany(
            User::class,
            'blocks',
            'user_nickname',  // this users nickname
            'blockingUser',   // blocking users nickname
            'nickname',
            'nickname'
        );
    }

    public function hasBlocked(User $otherUser): bool
    {
        return $this->blockedUsers()
            ->where('user_nickname', $otherUser->nickname)
            ->exists();
    }

    public function isBlockedBy(User $otherUser): bool
    {
        return $this->blockedBy()
            ->where('blockingUser', $otherUser->nickname)
            ->exists();
    }

    public function likes() : HasMany 
    {
        return $this->hasMany(Like::class);
    }

    public function comments() : HasMany {
        return $this->hasMany(Comment::class);
    }

    public function followers() : BelongsToMany {
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'follower_id');
    }

    public function following() : BelongsToMany {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'user_id');
    }

    public function isFollowedBy($otherUser) : bool {
        return $this->followers()
            ->where('follower_id', $otherUser->id)
            ->exists();
    }

    public function isFollowing($otherUser) : bool {
        return $this->following()
            ->where('user_id', $otherUser->id)
            ->exists();
    }

    public function notifications() : HasMany {
        return $this->hasMany(Notification::class, 'notification_owner_id', 'id');
    }
}
