<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\RattingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WisataController;

Route::get('/', function () {
    return redirect()->route('wisata.index');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::resource('wisata', WisataController::class)->parameters([
    'wisata' => 'wisata',
]);

Route::resource('kategori', KategoriController::class)->parameters([
    'kategori' => 'kategori',
]);

Route::resource('users', UserController::class)->parameters([
    'users' => 'user',
]);

Route::resource('rattings', RattingController::class)->parameters([
    'rattings' => 'ratting',
]);
