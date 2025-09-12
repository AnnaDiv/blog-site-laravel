<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntriesController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;

Route::get('/', [EntriesController::class, 'browse'])->name('home');

//etc views
Route::get('contact_us', function () {
    return view('etc.contact_us');
})->name('contact_us');

Route::middleware('guest')->group(function () { // only someone who isnt logged in can visit them
    Route::get('/register', [RegisterController::class, 'register'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'loginAuth'])->name('login.auth');
});

Route::middleware('auth')->group(function () { // only someone who is logged in can visit this
    //Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
