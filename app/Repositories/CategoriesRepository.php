<?php

namespace App\Repositories;

use App\Models\Category;

class CategoriesRepository
{

    public function browse(int $perPage)
    {
        $query = Category::query(); //->select('*'); same thing
        return $categories = $query->paginate($perPage);
    }

    public function search(int $perPage, string $quote)
    {
        $like = '%' . $quote . '%';
        $categories = Category::query()->select('*')
            ->where('title', 'like', $like)
            ->paginate($perPage);
        return $categories;
    }
}
