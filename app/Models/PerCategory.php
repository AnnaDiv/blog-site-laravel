<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerCategory extends Model
{
    use HasFactory;

    protected $table = 'percategory';

    protected $fillable = [
        'post_id',
        'category_title'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
