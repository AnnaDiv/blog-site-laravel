<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\EntriesRepository;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\Category;
use App\Support\ImageCreator;

class TokenController extends Controller
{
    public function login(Request $request, TokenService $tokenService): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)
                    ->where('status', 'active')
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $tokenService->generateToken($user->id, $user->nickname);

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => [
                'id'       => $user->id,
                'nickname' => $user->nickname,
                'image'    => $user->image_folder
            ]
        ]);
    }

    public function allPosts(Request $request) : JsonResponse {

        $posts = app(\App\Repositories\EntriesRepository::class)
                ->allPostsPerUser($request->user()->id);

        return response()->json($posts);
    }

    public function publicPosts(Request $request) : JsonResponse {
        $posts = app(\App\Repositories\EntriesRepository::class)
                ->publicPosts($request->user()->id);

        return response()->json($posts);
    }

    public function privatePosts(Request $request) : JsonResponse {
        $posts = app(\App\Repositories\EntriesRepository::class)
                ->privatePosts($request->user()->id);

        return response()->json($posts);
    }

    public function postView($post_id, Request $request) : JsonResponse{

        $post = app(\App\Repositories\EntriesRepository::class)
                ->postForApi($post_id, $request->user());

        return response()->json($post);
    }

    public function createPost(Request $request, ImageCreator $creator, EntriesRepository $entriesRepository) : JsonResponse {
        
        //user
        $user = $request->user();

        //all inputs
        $title = ($request->input('title') ?? '');
        $description = ($request->input('description') ?? '');
        $categories = json_decode($request->input('categories'), true);
        $post_status = $request->input('post_status');
        $type = $request->input('post_type');
        
        $all_cats = Category::query()
            ->pluck('title')
            ->toArray();
        
        $final_categories = [];

        foreach ($categories as $category) {

            $category = ucfirst(strtolower($category));
            if (in_array($category, $all_cats)) {
                $final_categories[] = ucfirst(strtolower($category));
            } else {
                $category = ucfirst(strtolower($category));
                Category::create([
                    'title' => $category,
                    'description' => ' ',
                ]);

                $final_categories[] = ucfirst(strtolower($category));
            }
        }
        if (empty($final_categories)) {
            $final_categories[] = 'None';
        }

        if ($post_status !== 'public' && $post_status !== 'private') {
            $post_status = 'private';
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
        } else {
            return response()->json('image missing');
        }

        if (!empty($title) && !empty($description) && !empty($image)) {

            $imageSubmit = $creator->createImage($image, $user->id);

            if (!empty($imageSubmit)) {
                $post = $entriesRepository->finalizing_posting($imageSubmit, $user->id, $user->nickname, $title, $description, $final_categories, $post_status, $type);

                if ($post !== false) {        
                    return response()->json($post);
                } else {
                    $errors[] = "entry couldnt be created";
                }
            } else {
                $errors[] = "something is wrong with your image, submit a different one";
            }
        } else {
            $errors[] = "not everything is filled";
        }

        return response()->json(['error'=> $errors]);
    }
}