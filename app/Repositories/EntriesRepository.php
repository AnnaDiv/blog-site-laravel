<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\Post;
use App\Models\User;
use App\Models\PerCategory;
use App\Models\Category;

class EntriesRepository
{

    public function browse(int $perPage, array $excludedUsers = [])
    {

        $query = Post::query()
            ->select([
                'posts.id',
                'posts.user_nickname',
                'posts.title',
                'posts.content',
                'posts.image_folder',
                'posts.likes',
                'posts.comments',
                DB::raw('DATE_FORMAT(posts.time, "%Y-%m-%dT%H:%i:%s") AS time'),
            ])
            ->with('percategory')
            ->where('deleted', false)
            ->where('status', 'public')
            ->where('type', 'post');

        if (!empty($excludedUsers)) {
            $query->whereNotIn('user_nickname', $excludedUsers);
        }

        $posts = $query->paginate($perPage);
        return $posts;
    }

    public function excludedUsers(string|bool $nickname)
    {
        $user = User::where('nickname', $nickname)->first();

        if (!$user) {
            return [];
        }

        $blockedUsers = $user->blockedUsers()
            ->wherePivot('status', 1)
            ->pluck('nickname')
            ->all();
        $blockedByUsers = $user->blockedBy()
            ->wherePivot('status', 1)
            ->pluck('nickname')
            ->all();

        return array_values(array_unique(array_merge($blockedUsers, $blockedByUsers)));
    }

    public function search(int $perPage, string $quote, array $excludedUsers)
    {
        $like = '%' . $quote . '%';
        $query = Post::query()->select('*')
            ->where('title', 'like', $like)
            ->orWhere('content', 'like', $like)
            ->orWhere('user_nickname', 'like', $like);

        if (!empty($excludedUsers)) {
            $query->whereNotIn('user_nickname', $excludedUsers);
        }
        $posts  = $query->paginate($perPage);
        return $posts;
    }

    public function finalizing_posting(array $res, int $user_id, string $nickname, string $title, string $description, array $categories, string $post_status, string $type)
    {
        //after post has been made
        //placing image into our final destination for it in our server storage
        $savePath = Storage::disk('public')->path(str_replace('storage/', '', $res['image_folder']));
        $image_uploaded = imagejpeg($res['new_image'], $savePath);

        //cleanup
        imagedestroy($res['old_image']);
        imagedestroy($res['new_image']);

        if ($image_uploaded === true) {
            $post = $this->createPost($user_id, $nickname, $title, $description, $categories, $res['image_folder'], $post_status, $type);
        } else {
            return false;
        }
        return $post;
    }

    public function createPost(int $user_id, string $nickname, string $title, string $content, array $categories, string $image_folder, string $status, string $type)
    {

        $res = [];

        $post = Post::create([
            'user_nickname' => $nickname,
            'title' => $title,
            'content' => $content,
            'image_folder' => $image_folder,
            'status' => $status,
            'type' => $type
        ]);

        $post_id = $post->id;

        foreach ($categories AS $category){
            $category = ucfirst(trim($category));
            $category = Category::firstOrCreate(['title' => $category]);

            PerCategory::create([
                'category_id' => $category->id,
                'post_id' => $post_id
            ]);

        }

        return $post;
    }
}
