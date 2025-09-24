<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Models\User;

class UsersRepository
{

    public function search(int $perPage, string $quote, array $excludedUsers)
    {
        $like = '%' . $quote . '%';
        $query = User::query()
            ->select([
                'users.nickname',
                'users.motto',
                'users.image_folder'
            ])
            ->withCount(['likes', 'comments', 'posts'])
            ->where('status', 'active')
            ->where(function ($q) use ($like) {
                $q->where('nickname', 'like', $like)
                    ->orWhere('motto', 'like', $like);
            });

        if (!empty($excludedUsers)) {
            $query->whereNotIn('nickname', $excludedUsers);
        }
        $users  = $query->paginate($perPage);
        //dd($users);
        return $users;
    }

    public function excludedUsers(string $nickname): array
    {
        $user = User::where('nickname', $nickname)->first();

        $blockedUsers = $user->blockedUsers()
            ->wherePivot('status', 1)
            ->pluck('nickname')
            ->all();
        $blockedBy = $user->blockedBy()
            ->wherePivot('status', 1)
            ->pluck('nickname')
            ->all();
        $excludedUsers = array_values(array_unique(array_merge($blockedUsers, $blockedBy)));
        //dd($excludedUsers);
        return $excludedUsers;
    }
}
