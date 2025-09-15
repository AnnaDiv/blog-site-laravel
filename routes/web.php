<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntriesController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\UsersController;

Route::get('/', [EntriesController::class, 'browse'])->name('home');

Route::get('/search/users', [UsersController::class, 'search'])->name('search.users');
Route::get('/search/posts', [EntriesController::class, 'search'])->name('search.posts');
Route::get('/post/view/{post}', [EntriesController::class, 'view'])->name('post.view');

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
    Route::get('/post/create', [EntriesController::class, 'create'])->name('post.create');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
