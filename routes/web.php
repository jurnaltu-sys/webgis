<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\RattingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WisataController;
use App\Http\Controllers\Wisatawan\DashboardWisatawanController;
use App\Http\Controllers\Wisatawan\RattingWisatawanController;
use App\Http\Controllers\UsersImportController;

Route::get('/', function () {
    $role = session('user_role');

    if ($role === 'wisatawan') {
        return redirect()->route('dashboard-wisatawan.index');
    }

    if ($role === 'admin') {
        return redirect()->route('wisata.index');
    }

    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::resource('wisata', WisataController::class)->parameters([
    'wisata' => 'wisata',
]);

Route::get('admin/wisata/import', [WisataController::class, 'showImportForm'])->name('wisata.import.form');
Route::post('admin/wisata/import', [WisataController::class, 'import'])->name('wisata.import');

Route::delete('wisata/{wisata}/foto/{foto}', [WisataController::class, 'destroyFoto'])
    ->name('wisata.foto.destroy');

Route::patch('wisata/{wisata}/foto/{foto}/cover', [WisataController::class, 'setCover'])
    ->name('wisata.foto.cover');

Route::resource('kategori', KategoriController::class)->parameters([
    'kategori' => 'kategori',
]);

Route::resource('users', UserController::class)->parameters([
    'users' => 'user',
]);

Route::resource('rattings', RattingController::class)->parameters([
    'rattings' => 'ratting',
]);

Route::resource('rattings-wisatawan', RattingWisatawanController::class)->parameters([
    'rattings-wisatawan' => 'rattings_wisatawan',
]);

Route::get('dashboard-wisatawan', [DashboardWisatawanController::class, 'index'])
    ->name('dashboard-wisatawan.index');

Route::get('admin/users/import', [UsersImportController::class, 'showForm'])
    ->name('users.import.form');

Route::post('admin/users/import', [UsersImportController::class, 'import'])
    ->name('users.import');
