<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

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
        //dd($posts);
        return $posts;
    }

    public function excludedUsers(string|bool $nickname)
    {
        if ($nickname === false) {
            return [];
        }
        $query = Post::query()
            ->select([
                'blocks.user_nickname',
                'blocks.blockingUser'
            ])
            ->where('user_nickname', $nickname)
            ->where('blockingUser', $nickname)
            ->where('status', 1);

        $names = $query->get();

        $excludedUsers = [];

        if (!empty($names)) {
            foreach ($names as $name) {
                if ($name['user_nickname'] === $nickname) {
                    $excludedUsers[] = $name['blockingUser'];
                } elseif ($name['blockingUser'] === $nickname) {
                    $excludedUsers[] = $name['user_nickname'];
                }
            }
        }
        return $excludedUsers;
    }
}
