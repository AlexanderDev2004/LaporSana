<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeknisiController;
use App\Http\Controllers\UserController;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

$router->pattern('id', '[0-9]+');

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin'])->name('postlogin');
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Rute untuk Admin (role 1)
Route::middleware(['auth', 'authorize:1'])->group(function () {
    Route::get('/admin/dashboard',[UserController::class,'index'])->name('admin.dashboard');
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});

// Rute untuk Mahasiswa (role 2)
Route::middleware(['authorize:2'])->group(function () {
    Route::get('/mahasiswa/dashboard', function () {
        return view('mahasiswa.dashboard');
    })->name('mahasiswa.dashboard');
});

// Rute untuk Dosen (role 3)
Route::middleware(['authorize:3'])->group(function () {
    Route::get('/dosen/dashboard', function () {
        return view('dosen.dashboard');
    })->name('dosen.dashboard');
});

// Rute untuk Tendik (role 4), Sarana (role 5), Teknis (role 6)
Route::middleware(['authorize:4'])->group(function () {
    Route::get('/tendik/dashboard', function () {
        return view('tendik.dashboard');
    })->name('tendik.dashboard');
});
Route::middleware(['authorize:5'])->group(function () {
    Route::get('/sarana/dashboard', function () {
        return view('sarana.dashboard');
    })->name('sarana.dashboard');
});
Route::middleware(['authorize:6'])->group(function () {
     Route::get('/teknisi/dashboard',[TeknisiController::class,'index'])->name('teknisi.dashboard');
      Route::get('/teknisi/tugas', [TeknisiController::class, 'tugas'])->name('teknisi.tugas');
      Route::get('/teknisi/riwayat', [TeknisiController::class, 'riwayat'])->name('teknisi.riwayat');
   Route::get('/teknisi/laporan/{id}', [TeknisiController::class, 'show'])->name('teknisi.detail');


});
