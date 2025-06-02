<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LantaiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TeknisiController;
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

Route::group(['prefix' => 'teknisi', 'middleware' => 'authorize:6'], function () {
        Route::get('/dashboard', [TeknisiController::class, 'dashboard'])->name('teknisi.dashboard');
        Route::get('/', [TeknisiController::class, 'index'])->name('teknisi.index');       
        Route::get('/list', [TeknisiController::class, 'list'])->name('teknisi.list');           
        Route::post('/', [TeknisiController::class, 'store'])->name('teknisi.store');
        Route::get('/{teknisi}/edit', [TeknisiController::class, 'edit'])->name('teknisi.edit');
        Route::get('/{teknisi}/show', [TeknisiController::class, 'show'])->name('teknisi.show');
        Route::put('/{teknisi}', [TeknisiController::class, 'update'])->name('teknisi.update');
        Route::delete('/{teknisi}', [TeknisiController::class, 'destroy'])->name('teknisi.destroy');    
    });
