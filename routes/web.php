<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\LantaiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PelaporController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RekomendasiPerbaikan;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\SarprasController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\TeknisiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ValidlaporAController;
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
    Route::get('/admin/dashboard/spk', [DashboardController::class, 'hitungSPK']);
    // Route::get('/admin/dashboard/py', [RekomendasiPerbaikan::class, 'hitungSPK'])->name('calculate');
    Route::get('/admin/spk', [RekomendasiPerbaikan::class, 'tampilkanSPK'])->name('admin.spk');


    // Profile
    Route::group(['prefix' => 'admin/profile'], function () {
        Route::get('/profile', [ProfileController::class, 'show'])->name('admin.profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    });

    // User Management
    Route::group(['prefix' => 'admin/users'], function (): void {
        Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/list', [UserController::class, 'list'])->name('admin.users.list');
        Route::get('/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::get('/{user}/show', [UserController::class, 'show'])->name('admin.users.show');
        Route::get('/{user}/confirm', [UserController::class, 'confirm'])->name('admin.users.confirm');
        Route::delete('/{user}/delete', [UserController::class, 'delete'])->name('admin.users.delete');
         Route::get('/import', [UserController::class, 'import'])->name('admin.users.import');
        Route::post('/import_ajax', [UserController::class, 'import_ajax'])->name('admin.users.import_ajax');   
        Route::get('/export_excel', [UserController::class, 'export_excel'])->name('admin.users.export_excel');  
        Route::get('export_pdf', [UserController::class, 'export_pdf'])->name('admin.users.export_pdf');  
    });
    // Role Management

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
        Route::get('/import', [RoleController::class, 'import'])->name('admin.roles.import');
        Route::post('/import_ajax', [RoleController::class, 'import_ajax'])->name('admin.roles.import_ajax');   
        Route::get('/export_excel', [RoleController::class, 'export_excel'])->name('admin.roles.export_excel');  
        Route::get('export_pdf', [RoleController::class, 'export_pdf'])->name('admin.roles.export_pdf');       
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
        Route::get('/import', [LantaiController::class, 'import'])->name('admin.lantai.import');
        Route::post('/import_ajax', [LantaiController::class, 'import_ajax'])->name('admin.lantai.import_ajax');   
        Route::get('/export_excel', [LantaiController::class, 'export_excel'])->name('admin.lantai.export_excel');  
        Route::get('export_pdf', [LantaiController::class, 'export_pdf'])->name('admin.lantai.export_pdf');       
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
        Route::get('/import', [RuanganController::class, 'import'])->name('admin.ruangan.import');
        Route::post('/import_ajax', [RuanganController::class, 'import_ajax'])->name('admin.ruangan.import_ajax');  
        Route::get('/export_excel', [RuanganController::class, 'export_excel'])->name('admin.ruangan.export_excel'); 
        Route::get('export_pdf', [RuanganController::class, 'export_pdf'])->name('admin.ruangan.export_pdf');      
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
        Route::get('/import', [FasilitasController::class, 'import'])->name('admin.fasilitas.import');
        Route::post('/import_ajax', [FasilitasController::class, 'import_ajax'])->name('admin.fasilitas.import_ajax');   
        Route::get('/export_excel', [FasilitasController::class, 'export_excel'])->name('admin.fasilitas.export_excel');  
        Route::get('export_pdf', [FasilitasController::class, 'export_pdf'])->name('admin.fasilitas.export_pdf');       
    });

    //Laporan Verifikasi
   Route::group(['prefix' => 'admin/validasi_laporan'], function () {
    Route::get('/', [LaporanController::class, 'index'])->name('admin.validasi_laporan.index');
    Route::get('/list', [LaporanController::class, 'list'])->name('admin.validasi_laporan.list');
    Route::get('/{laporan}/show', [LaporanController::class, 'show'])->name('admin.validasi_laporan.show');
    Route::post('/{laporan}/verify', [LaporanController::class, 'verify'])->name('admin.validasi_laporan.verify');
    Route::get('/export_excel', [LaporanController::class, 'export_excel'])->name('admin.validasi_laporan.export_excel');  
    Route::get('export_pdf', [LaporanController::class, 'export_pdf'])->name('admin.validasi_laporan.export_pdf');  

    });
});


// Rute untuk Pelapor (role 2,3,4 : Mahasiswa, Dosen, Teknisi)
Route::middleware(['authorize:2,3,4'])->prefix('pelapor')->group(function () {
    Route::get('/dashboard', [PelaporController::class, 'index'])->name('pelapor.dashboard');
});



// Rute untuk Pelapor (role 2, 3, 4 : Mahasiswa, Dosen, Teknisi)
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

// Sarpras Management (role 5)
Route::middleware(['authorize:5'])->group(function () {
    Route::get('/sarpras/dashboard', [SarprasController::class, 'index'])->name('sarpras.dashboard');
    Route::get('/sarpras/profile', [SarprasController::class, 'show'])->name('sarpras.profile.show');
    Route::get('/sarpras/profile/edit', [SarprasController::class, 'edit'])->name('sarpras.profile.edit');
    Route::put('/sarpras/profile', [SarprasController::class, 'update'])->name('sarpras.profile.update');
});

// Rute untuk Teknisi (role 6)
Route::group(['prefix' => 'teknisi', 'middleware' => 'authorize:6'], function () {
        Route::get('/dashboard', [TeknisiController::class, 'dashboard'])->name('teknisi.dashboard'); 
        Route::get('/profile', [TeknisiController::class, 'showProfile'])->name('teknisi.profile.show');
        Route::get('/profile/edit', [TeknisiController::class, 'editProfile'])->name('teknisi.profile.edit');
        Route::put('/profile', [TeknisiController::class, 'updateProfile'])->name('teknisi.profile.update');
        Route::get('/', [TeknisiController::class, 'index'])->name('teknisi.index');
        Route::get('/list', [TeknisiController::class, 'list'])->name('teknisi.list');
        Route::post('/', [TeknisiController::class, 'store'])->name('teknisi.store');
        Route::get('/{teknisi}/edit', [TeknisiController::class, 'edit'])->name('teknisi.edit');
        Route::get('/{teknisi}/editpemeriksaan', [TeknisiController::class, 'editPemeriksaan'])->name('teknisi.editpemeriksaan');
        Route::get('/{teknisi}/show', [TeknisiController::class, 'show'])->name('teknisi.show');
        Route::put('/{teknisi}', [TeknisiController::class, 'update'])->name('teknisi.update');
        Route::put('/{teknisi}/updatepemeriksaan', [TeknisiController::class, 'updatePemeriksaan'])->name('teknisi.updatepemeriksaan');
        Route::get('/teknisi/riwayat', [TeknisiController::class, 'riwayat'])->name('teknisi.riwayat');
        Route::get('/teknisi/riwayat/list', [TeknisiController::class, 'riwayatList'])->name('teknisi.riwayat.list');
        Route::get('/laporan/{id}', [TeknisiController::class, 'showLaporan'])->name('teknisi.show_laporan');
        
});


Route::get('/python', [RekomendasiPerbaikan::class, 'hitungSPK'])->name('calculate');
