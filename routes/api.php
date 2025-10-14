<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Http\Request;

Route::post('/token/login', [TokenController::class, 'login'])->name('api.login');

Route::middleware('auth.jwt')->group(function () { //api routes csrf token free

    Route::get('/token/user', function (Request $request) {
        return response()->json([
            'id' => $request->user()
        ]); // returned by the middleware
    });

    Route::get('token/posts/all', [TokenController::class, 'allPosts'])->name('api.posts.all');
    Route::get('token/posts/public', [TokenController::class, 'publicPosts'])->name('api.posts.public');
    Route::get('token/posts/private', [TokenController::class, 'privatePosts'])->name('api.posts.private');

    Route::get('token/post/{post_id}', [TokenController::class, 'postView'])->name('api.post.view');

    Route::post('token/post/create', [TokenController::class, 'createPost'])->name('api.post.create');
});