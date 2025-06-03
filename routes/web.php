<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelaporController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SarprasController;
use App\Http\Controllers\UserController;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
    // Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // User Management
    Route::group(['prefix' => 'admin/users'], function () {
        Route::get('/', [UserController::class, 'list'])->name('admin.users.index');
        Route::get('/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/{user}', [UserController::class, 'show'])->name('admin.users.show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Role Management
    Route::group(['prefix' => 'admin/roles'], function () {
        Route::get('/', [RoleController::class, 'index'])->name('admin.roles.index');
        Route::get('/create', [RoleController::class, 'create'])->name('admin.roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('admin.roles.store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
        Route::get('/{role}', [RoleController::class, 'show'])->name('admin.roles.show');
        Route::put('/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
    });
});

// Rute untuk Pelapor (role 2)
Route::middleware(['authorize:2,3,4'])->group(function () {
    Route::group(['prefix' => 'pelapor'], function () {
        Route::get('/pelapor/dashboard', [PelaporController::class, 'index'])->name('pelapor.dashboard');
    });
});

// Rute untuk Dosen (role 3)
Route::middleware(['authorize:3'])->group(function () {
    Route::get('/pelapor/dashboard', function () {

        return view('pelapor.dashboard');
    })->name('pelapor.dashboard');
});

// Rute untuk Tendik (role 4), Sarana (role 5), Teknis (role 6)
Route::middleware(['authorize:4'])->group(function () {
    Route::get('/pelapor/dashboard', function () {
        return view('pelapor.dashboard');
    })->name('pelapor.dashboard');
});

Route::middleware(['authorize:5'])->group(callback: function () {
        Route::get('/sarpras/dashboard', [SarprasController::class, 'index'])->name('sarpras.dashboard');
        Route::get('/sarpras/profile', [SarprasController::class, 'show'])->name('sarpras.profile.show');
        Route::get('/sarpras/profile/edit', [SarprasController::class, 'edit'])->name('sarpras.profile.edit');
        Route::put('/sarpras/profile', [SarprasController::class, 'update'])->name('sarpras.profile.update');
});
Route::middleware(['authorize:6'])->group(function () {
    Route::get('/teknis/dashboard', function () {
        return view('teknisi.dashboard');
    })->name('teknis.dashboard');
});
