<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\LantaiController;
use App\Http\Controllers\PelaporController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\SarprasController;
use App\Http\Controllers\TeknisiController;
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

      //Fasilitas Management
        Route::group(['prefix' => 'admin/fasilitas'], function () {
        Route::get('/', [FasilitasController::class, 'index'])->name('admin.fasilitas.index');
        Route::get('/list', [FasilitasController::class, 'list'])->name('admin.fasilitas.list');
        Route::get('/create', [FasilitasController::class, 'create'])->name('admin.fasilitas.create');
        Route::post('/', [FasilitasController::class, 'store'])->name('admin.fasilitas.store');
        Route::get('/{fasilitas}/edit', [FasilitasController::class, 'edit'])->name('admin.fasilitas.edit');
        Route::put('/{fasilitas}', [FasilitasController::class, 'update'])->name('admin.fasilitas.update');
        Route::get('/{fasilitas}/show', [FasilitasController::class, 'show'])->name('admin.fasilitas.show');
        Route::get('/{fasilitas}/confirm', [FasilitasController::class, 'confirm'])->name('admin.fasilitas.confirm');
        Route::delete('/{fasilitas}/delete', [FasilitasController::class, 'delete'])->name('admin.fasilitas.delete');
    });


});


// Rute untuk Pelapor (role 2)
Route::middleware(['authorize:2,3,4'])->prefix('pelapor')->group(function () {
    Route::get('/dashboard', [PelaporController::class, 'index'])->name('pelapor.dashboard');
});

Route::middleware(['authorize:5'])->group(callback: function () {
        Route::get('/sarpras/dashboard', [SarprasController::class, 'index'])->name('sarpras.dashboard');
        Route::get('/sarpras/profile', [SarprasController::class, 'show'])->name('sarpras.profile.show');
        Route::get('/sarpras/profile/edit', [SarprasController::class, 'edit'])->name('sarpras.profile.edit');
        Route::put('/sarpras/profile', [SarprasController::class, 'update'])->name('sarpras.profile.update');
});

// Rute untuk Pelapor (role 2, 3, 4)
Route::middleware(['authorize:2,3,4'])->group(function () {
    Route::group(['prefix' => 'pelapor'], function () {
        Route::get('/dashboard', [PelaporController::class, 'index'])->name('pelapor.dashboard');
        Route::get('/laporan', [PelaporController::class, 'laporan'])->name('pelapor.laporan');
        Route::POST('/laporan/list', [PelaporController::class, 'list'])->name('pelapor.list');
        Route::get('/create', [PelaporController::class, 'create'])->name('pelapor.create');
        Route::post('/store', [PelaporController::class, 'store'])->name('pelapor.store');
        Route::get('/laporan/{laporan_id}', [PelaporController::class, 'show'])->name('pelapor.show');
        Route::get('/laporan_bersama', [PelaporController::class, 'laporanBersama'])->name('pelapor.laporan_bersama');
        Route::POST('/laporan/list_bersama', [PelaporController::class, 'listBersama'])->name('pelapor.list.bersama');
        Route::get('/laporan_bersama/{laporan_id}', [PelaporController::class, 'showBersama'])->name('pelapor.show.bersama');
    });
});


Route::group(['prefix' => 'teknisi', 'middleware' => 'authorize:6'], function () {
        Route::get('/dashboard', [TeknisiController::class, 'dashboard'])->name('teknisi.dashboard');
        Route::get('/', [TeknisiController::class, 'index'])->name('teknisi.index');
        Route::get('/list', [TeknisiController::class, 'list'])->name('teknisi.list');
        Route::post('/', [TeknisiController::class, 'store'])->name('teknisi.store');
        Route::get('/{teknisi}/edit', [TeknisiController::class, 'edit'])->name('teknisi.edit');
        Route::get('/{teknisi}/show', [TeknisiController::class, 'show'])->name('teknisi.show');
        Route::put('/{teknisi}/confirm', [TeknisiController::class, 'update'])->name('teknisi.update');
        Route::delete('/{teknisi}/delete', [TeknisiController::class, 'destroy'])->name('teknisi.destroy');
        Route::get('/teknisi/riwayat', [TeknisiController::class, 'riwayat'])->name('teknisi.riwayat');
        Route::get('/teknisi/riwayat/list', [TeknisiController::class, 'riwayatList'])->name('teknisi.riwayat.list');
    });
