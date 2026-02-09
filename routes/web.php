<?php

// Route proses rekomendasi wisatawan
Route::get('/wisatawan/rekomendasi/proses', [App\Http\Controllers\RekomendasiController::class, 'proses'])->name('wisatawan.rekomendasi.proses');

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\RattingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WisataController;
use App\Http\Controllers\Wisatawan\DashboardWisatawanController;
use App\Http\Controllers\Wisatawan\RattingWisatawanController;
use App\Http\Controllers\UsersImportController;
use App\Http\Controllers\RattingsImportController;
use App\Http\Controllers\Wisatawan\RattingDatasetWisatawanController;


// Form input/edit seluruh ratting 1 user (pivot style)
Route::get('admin/rattings/exceluser/{user}/edit', [RattingController::class, 'editExcelUser'])->name('rattings.exceluser.edit');
Route::post('admin/rattings/exceluser/{user}/edit', [RattingController::class, 'updateExcelUser'])->name('rattings.exceluser.update');
Route::delete('admin/rattings/exceluser/{user}/delete', [RattingController::class, 'deleteExcelUser'])->name('rattings.exceluser.delete');

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

// Wisatawan hanya bisa lihat (index, show)
Route::resource('wisatawan-wisata', App\Http\Controllers\Wisatawan\WisatawanWisataController::class)
    ->only(['index', 'show'])
    ->parameters(['wisatawan-wisata' => 'wisata']);

// Halaman Rekomendasi untuk wisatawan
use App\Http\Controllers\Wisatawan\RekomendasiWisatawanController;
Route::get('rekomendasi-wisatawan', [RekomendasiWisatawanController::class, 'index'])
    ->name('rekomendasi-wisatawan.index');

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

Route::get('admin/rattings/excelview', [App\Http\Controllers\Admin\RattingController::class, 'excelView'])
    ->name('rattings.excelview');

Route::resource('rattings-wisatawan', RattingWisatawanController::class)->parameters([
    'rattings-wisatawan' => 'rattings_wisatawan',
]);

Route::get('dashboard-wisatawan', [DashboardWisatawanController::class, 'index'])
    ->name('dashboard-wisatawan.index');

Route::get('admin/users/import', [UsersImportController::class, 'showForm'])
    ->name('users.import.form');

Route::post('admin/users/import', [UsersImportController::class, 'import'])
    ->name('users.import');

Route::get('admin/rattings/import', [RattingsImportController::class, 'showForm'])
    ->name('rattings.import.form');

Route::post('admin/rattings/import', [RattingsImportController::class, 'import'])
    ->name('rattings.import');

// Ratting Format Dataset untuk wisatawan (lihat-only)
Route::get('wisatawan/rattings/dataset', [App\Http\Controllers\Wisatawan\RattingDatasetWisatawanController::class, 'index'])
    ->name('wisatawan.rattings.dataset');
Route::get('wisatawan/rattings/dataset/export', [App\Http\Controllers\Wisatawan\RattingDatasetWisatawanController::class, 'exportExcel'])
    ->name('wisatawan.rattings.dataset.export');
