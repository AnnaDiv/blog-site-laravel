<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Post;

class Category extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $table = 'categories';

    protected $fillable = [
        'title',
        'description'
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'percategory', 'category_id', 'post_id');
    }
}
