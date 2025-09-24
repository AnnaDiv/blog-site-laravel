<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'user_nickname',
        'title',
        'content',
        'image_folder',
        'likes_count',
        'comments_count',
        'status',
        'deleted',
        'type'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'percategory', 'post_id', 'category_id');
    }

    /*
    public function percategory()
    {
        return $this->hasMany(PerCategory::class);
    }
    */

    public function likes() : HasMany {
        return $this->hasMany(Like::class);
    }

    public function comments() : HasMany {
        return $this->hasMany(Comment::class);
    }
}
