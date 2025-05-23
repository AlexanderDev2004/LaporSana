<?php

use App\Http\Controllers\AuthController;
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

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postregister'])->name('postregister');
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin'])->name('postlogin');
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Rute untuk Admin (role 1)
Route::middleware(['auth', 'authorize:1'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('user.index');
    })->name('admin.dashboard');
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
    Route::get('/teknis/dashboard', function () {
        return view('teknis.dashboard');
    })->name('teknis.dashboard');
});
