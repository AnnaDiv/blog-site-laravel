<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\EntriesRepository;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
}