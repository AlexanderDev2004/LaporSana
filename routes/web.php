<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LantaiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\UserController;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Http\Parser\RouteParams;

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

    

    // Role Management
    Route::group(['prefix' => 'admin/roles'], function (): void {
        Route::get('/', [RoleController::class, 'index'])->name('admin.roles.index');
        Route::get('/list', [RoleController::class, 'list'])->name('admin.roles.list');
        Route::get('/create', [RoleController::class, 'create'])->name('admin.roles.create');
        Route::post('/', [RoleController::class, 'store'])->name('admin.roles.store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
        Route::get('/{role}/show', [RoleController::class, 'show'])->name('admin.roles.show');
        Route::get('/{role}/confirm', [RoleController::class, 'confirm'])->name('admin.roles.confirm');
        Route::delete('/{role}/delete', [RoleController::class, 'delete'])->name('admin.roles.delete');
    });

    //Lantai Management
        Route::group(['prefix' => 'admin/lantai'], function () {
        Route::get('/', [LantaiController::class, 'index'])->name('admin.lantai.index');
        Route::get('/list', [LantaiController::class, 'list'])->name('admin.lantai.list');
        Route::get('/create', [LantaiController::class, 'create'])->name('admin.lantai.create');
        Route::post('/', [LantaiController::class, 'store'])->name('admin.lantai.store');
        Route::get('/{lantai}/edit', [LantaiController::class, 'edit'])->name('admin.lantai.edit');
        Route::put('/{lantai}', [LantaiController::class, 'update'])->name('admin.lantai.update');
        Route::get('/{lantai}/show', [LantaiController::class, 'show'])->name('admin.lantai.show');
        Route::get('/{lantai}/confirm', [LantaiController::class, 'confirm'])->name('admin.lantai.confirm');
        Route::delete('/{lantai}/delete', [LantaiController::class, 'delete'])->name('admin.lantai.delete');
    });

       //Ruangan Management
        Route::group(['prefix' => 'admin/ruangan'], function () {
        Route::get('/', [RuanganController::class, 'index'])->name('admin.ruangan.index');
        Route::get('/list', [RuanganController::class, 'list'])->name('admin.ruangan.list');
        Route::get('/create', [RuanganController::class, 'create'])->name('admin.ruangan.create');
        Route::post('/', [RuanganController::class, 'store'])->name('admin.ruangan.store');
        Route::get('/{ruangan}/edit', [RuanganController::class, 'edit'])->name('admin.ruangan.edit');
        Route::put('/{ruangan}', [RuanganController::class, 'update'])->name('admin.ruangan.update');
        Route::get('/{ruangan}/show', [RuanganController::class, 'show'])->name('admin.ruangan.show');
        Route::get('/{ruangan}/confirm', [RuanganController::class, 'confirm'])->name('admin.ruangan.confirm');
        Route::delete('/{ruangan}/delete', [RuanganController::class, 'delete'])->name('admin.ruangan.delete');
    });
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
