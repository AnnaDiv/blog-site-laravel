<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Post;
use App\Models\User;
use App\Models\PerCategory;
use App\Models\Category;

class EntriesRepository
{

    protected $perPage = 15;

    public function browse(int $perPage, array $excludedUsers = [])
    {

        $query = Post::query()
            ->select([
                'posts.id',
                'posts.user_nickname',
                'posts.title',
                'posts.content',
                'posts.image_folder',
                'posts.likes_count',
                'posts.comments_count',
                DB::raw('DATE_FORMAT(posts.time, "%Y-%m-%dT%H:%i:%s") AS time'),
            ])
            ->with('categories')
            ->where('deleted', false)
            ->where('status', 'public')
            ->where('type', 'post');

        if (!empty($excludedUsers)) {
            $query->whereNotIn('user_nickname', $excludedUsers);
        }

        $posts = $query->paginate($perPage);
        return $posts;
    }

    public function allPublicPosts() {

        $query = Post::with('categories')
            ->with('likes')
            ->with('comments')
            ->where('deleted', false)
            ->where('status', 'public')
            ->where('type', 'post');

        $posts = $query->paginate(15);
        return $posts;
    }

    public function allPublicPostsByUser(string $user_nickname) {

        $query = Post::with('categories')
            ->with('likes')
            ->with('comments')
            ->where('user_nickname', $user_nickname)
            ->where('deleted', false)
            ->where('status', 'public')
            ->where('type', 'post');

        $posts = $query->paginate(15);
        return $posts;
    }

    public function excludedUsers(string|bool $nickname)
    {
        $user = User::where('nickname', $nickname)->first();

        if (!$user) {
            return [];
        }

        $blockedUsers = $user->blockedUsers()
            ->pluck('nickname')
            ->all();
        $blockedByUsers = $user->blockedBy()
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

            $post->categories()->attach($category->id);
        }

        return $post;
    }


    protected function postQuery(User $user)
    {
        return Post::select([
                'posts.id',
                'posts.user_nickname',
                'posts.title',
                'posts.content',
                'posts.image_folder',
                'posts.status',
            ])
            ->with('categories')
            ->where('user_nickname', $user->nickname);
    }

    public function allPostsPerUser($user_id){

        $user = User::findOrFail($user_id);

        $posts = Post::select('*')
            ->with('categories')
            ->where('user_nickname', $user->nickname)
            ->paginate(15);
        
        return $posts;
    }

    public function deletedPostsPerUser($user_id) {
        $user = User::findOrFail($user_id);

        $posts = Post::with('categories')
            ->where('user_nickname', $user->nickname)
            ->where('deleted', 1)
            ->paginate(15);
        
        return $posts;
    }

    public function deletedPostsbyQuote($perPage, $quote) {

        $like = '%' . $quote . '%';

        $posts = Post::with('categories')
            ->where('deleted', 1)
            ->where(function ($query) use ($like) {
            $query->where('title', 'like', $like)
                ->orWhere('content', 'like', $like)
                ->orWhere('user_nickname', 'like', $like);
    })->paginate($perPage);
        
        return $posts;
    }

    public function privatePosts($user_id) {

        $user = User::findOrFail($user_id);

        $posts = $this->postQuery($user)
            ->where('status', 'private')
            ->where('deleted', false)
            ->paginate(15);
        
        return $posts;
    }

    public function publicPosts($user_id) {
        
        $user = User::findOrFail($user_id);

        $posts = $this->postQuery($user)
            ->where('status', 'public')
            ->where('deleted', false)
            ->paginate(15);

        return $posts;
    }

    public function categoryPosts(string $category_id, array $excludedUsers) {

        $category = Category::where('id', $category_id)->first();

        if (!$category){
            return null;
        }

        $query = Post::query()->select('*')
            ->with('categories') 
            ->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.id', $category->id);
        });

        if (!empty($excludedUsers)){
            $query->whereNotIn('user_nickname', $excludedUsers);
        }

        return $query->paginate(15);

    }

    public function update($post, $imageSubmit, string $title, string $content, array $categories, string $status) {

        if($imageSubmit!=false){
            Storage::disk('public')->delete($post->image_folder);

            $savePath = Storage::disk('public')->path(str_replace('storage/', '', $imageSubmit['image_folder']));
            $image_uploaded = imagejpeg($imageSubmit['new_image'], $savePath);

            //cleanup
            imagedestroy($imageSubmit['old_image']);
            imagedestroy($imageSubmit['new_image']);

            $image_folder = $imageSubmit['image_folder'];
        }
        else{
            $image_folder = '';
        }

        $categories_ids = [];

        //attaching new
        foreach ($categories AS $category){
            $category = ucfirst(trim($category));
            $category = Category::firstOrCreate(['title' => $category]);

            $categories_ids[] = $category->id;
        }

        //synch cats
        $post->categories()->sync($categories_ids);

        if (!empty($image_folder)){
            $post->update([
                'title' => $title,
                'content' => $content,
                'image_folder' => $image_folder,
                'status' => $status
            ]);
        }
        else {
            $post->update([
                'title' => $title,
                'content' => $content,
                'status' => $status
            ]);  
        }

        return $post;
    }

    public function likedPosts(User $user) {

        $excludedUsers = $this->excludedUsers($user->nickname);

        $query = Post::query()->select('*')
                ->whereHas('likes', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->with('categories');
        
        if (!empty($excludedUsers)) {
            $query->whereNotIn('user_nickname', $excludedUsers);
        }

        $posts = $query->paginate(15);

        return $posts;
    }

    public function deletedPosts() {
        $posts = Post::with('categories')
                ->where('deleted', 1)
                ->paginate(15);
        return $posts;
    }

    public function myart() {
        $posts = Post::with('categories')
                ->where('deleted', 0)
                ->where('type', 'art')
                ->get();
        return $posts;
    }

    public function myfeed(User $user) {

        $followed_users = $user->following()->pluck('users.nickname');
        $query = Post::with('categories')
                ->whereIn('user_nickname', $followed_users);
        $posts = $query->paginate(15);
        return $posts;
    }

    public function postForApi($post_id, User $user) {

        $post = Post::where('id', $post_id)
                ->where('user_nickname', $user->nickname)
                ->where('deleted', 0)
                ->firstorfail();
        return $post;
    }

}
