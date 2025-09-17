<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerCategory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'percategory';

    protected $fillable = [
        'post_id',
        'category_id'
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
