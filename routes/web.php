<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntriesController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\CommentController;

Route::get('/', [EntriesController::class, 'browse'])->name('home');

Route::get('/search/users', [UsersController::class, 'search'])->name('search.users');
Route::get('/search/posts', [EntriesController::class, 'search'])->name('search.posts');
Route::get('/post/view/{post}', [EntriesController::class, 'view'])->name('post.view');

Route::get('/like/{post}', [LikesController::class, 'getLikes'])->name('likes.get');
Route::post('/like/toggle', [LikesController::class, 'toggleLike'])->name('likes.toggle');

Route::get('/comment/{post}', [CommentController::class, 'getComments'])->name('comments.get');
Route::post('/comment/add', [CommentController::class, 'addComment'])->name('comment.add');
Route::post('/comment/remove', [CommentController::class, 'removeComment'])->name('comment.remove');

//etc views
Route::get('contact_us', function () {
    return view('etc.contact_us');
})->name('contact_us');
Route::get('/categories', [CategoriesController::class, 'index'])->name('categories');
Route::get('/categories/search', [CategoriesController::class, 'search'])->name('categories.search');

Route::middleware('guest')->group(function () { // only someone who isnt logged in can visit them
    Route::get('/register', [RegisterController::class, 'register'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'loginAuth'])->name('login.auth');
});

Route::middleware('auth')->group(function () { // only someone who is logged in can visit this
    Route::get('/post/create', [EntriesController::class, 'createPostView'])->name('post.createView');
    Route::post('/post/create', [EntriesController::class, 'create'])->name('post.create');
    Route::post('/image/preview', [ImageController::class, 'preview'])->name('image.preview');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
