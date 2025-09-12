<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Post;
use App\Models\Blocks;
use App\Models\User;

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
}
