<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\JwtController;
use Illuminate\Session\Middleware\StartSession;

/* ----------  public (jwt)  ---------- */
Route::post('/token/login', [TokenController::class, 'login'])->name('api.login');

/* ----------  session-guard JWT helper  ---------- */
Route::middleware(['auth:web', StartSession::class])->group(function () {

    Route::get('/token/jwt', [JwtController::class, 'token'])->name('jwt.token');
});

/* ----------  bearer-protected  ---------- */
Route::middleware('auth.jwt')->group(function () {

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

    Route::prefix('messages')->group(function () {
        Route::get ('/',       [MessageController::class, 'index']);
        Route::post('/start',  [MessageController::class, 'start']);
        Route::get ('/{conversation}', [MessageController::class, 'show']);
        Route::post('/{conversation}', [MessageController::class, 'store']);
        Route::put ('/{msg}/read', [MessageController::class, 'markRead']);
    });
});
