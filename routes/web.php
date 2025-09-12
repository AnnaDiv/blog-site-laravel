<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.index');
})->name('home');
Route::view('/main', 'home.index');
Route::view('/browse', 'home.index');

Route::get('contact_us', function () {
    return view('etc.contact_us');
})->name('contact_us');
