<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Support\ImageCreator;

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

    public function usersByQuote(int $perPage, string $quote) { //admin usage for users by quote

        $like = '%' . $quote . '%';
        $query = User::query('*')
            ->withCount(['likes', 'comments', 'posts'])
            ->where(function ($q) use ($like) {
                $q->where('nickname', 'like', $like)
                    ->orWhere('motto', 'like', $like);
            });
        return $query->paginate($perPage);
    }

    public function excludedUsers(string $nickname): array
    {
        $user = User::where('nickname', $nickname)->first();

        $blockedUsers = $user->blockedUsers()
            ->pluck('nickname')
            ->all();
        $blockedBy = $user->blockedBy()
            ->pluck('nickname')
            ->all();
        $excludedUsers = array_values(array_unique(array_merge($blockedUsers, $blockedBy)));
        //dd($excludedUsers);
        return $excludedUsers;
    }

    public function update(User $user, array $validatedData, $image) : User | string {
        
        if ($user->nickname != $validatedData['nickname']){
            $user_nickname_used = User::where('nickname', $validatedData['nickname'])->exists();

            if($user_nickname_used){
                return 'Nickname is already in use';
            }
        }

        if ($user->email != $validatedData['email']) {
            $user_email_used = User::where('email', $validatedData['email'])->exists();

            if($user_email_used){
                return 'Email is already in use';
            }
        }

        if ($validatedData['password'] != ''){
            if (auth()->user()->admin){
                $new_password = Hash::make($validatedData['password']);
            }
            elseif ($validatedData['old_pass'] != ''){

                if(Hash::check($validatedData['old_pass'], $user->password)){
                    $new_password = Hash::make($validatedData['password']);
                }
            }
            else {
                return 'Input old password to change it';
            }
        }
        else {
            $new_password = $user->password;
        }

        if ($image!=null){
            $creator = new ImageCreator();
            $imageSubmit = $creator->createImageProf($image, $user->id);
            if (!empty($imageSubmit)){

                $image_uploaded = $this->usersImageUpload($imageSubmit, $user);

                if ($image_uploaded === true) {
                    $user->update([
                        'nickname' => $validatedData['nickname'],
                        'email' => $validatedData['email'],
                        'image_folder' => $imageSubmit['image_folder'],
                        'motto' => $validatedData['motto'],
                        'password' => $new_password
                    ]);
                } else {
                    return 'couldnt update image';
                }
            }
        }
        else {
            $user->update([
                'nickname' => $validatedData['nickname'],
                'email' => $validatedData['email'],
                'motto' => $validatedData['motto'],
                'password' => $new_password
            ]);
        }
        
        return $user;
    }

    public function usersImageUpload($imageSubmit, $user) {
        if ($user->image_folder){
            Storage::disk('public')->delete($user->image_folder);
        }
        
        $savePath = Storage::disk('public')->path(str_replace('storage/', '', $imageSubmit['image_folder']));
        $image_uploaded = imagejpeg($imageSubmit['new_image'], $savePath);

        //cleanup
        imagedestroy($imageSubmit['old_image']);
        imagedestroy($imageSubmit['new_image']);

        return $image_uploaded;
    }
}
