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
    Route::get('/admin/spk', [RekomendasiPerbaikan::class, 'tampilkanSPK'])->name('admin.spk');
    Route::post('admin/perbarui-data', [RekomendasiPerbaikan::class, 'perbaruiData'])->name('perbarui.data');

    // Step-by-step SPK
    Route::group(['prefix' => 'admin/spk'], function () {
        Route::get('/spk_steps', [RekomendasiPerbaikan::class, 'showStepByStep'])->name('admin.spk.step_by_step');
    });

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

// Rute untuk Pelapor (role 2, 3, 4 : Mahasiswa, Dosen, Teknisi)
Route::middleware(['authorize:2,3,4'])->group(function () {
    Route::group(['prefix' => 'pelapor'], function () {
        Route::get('/dashboard', [PelaporController::class, 'index'])->name('pelapor.dashboard');
        Route::get('/pelapor/profile', [PelaporController::class, 'showProfile'])->name('pelapor.profile.show');
        Route::get('/pelapor/profile/edit', [PelaporController::class, 'edit'])->name('pelapor.profile.edit');
        Route::put('/pelapor/profile', [PelaporController::class, 'update'])->name('pelapor.profile.update');

        // laporan saya
        Route::get('/laporan', [PelaporController::class, 'laporan'])->name('pelapor.laporan');
        Route::POST('/laporan/list', [PelaporController::class, 'list'])->name('pelapor.list');
        Route::get('/create', [PelaporController::class, 'create'])->name('pelapor.create');
        Route::post('/store', [PelaporController::class, 'store'])->name('pelapor.store');
        Route::get('/laporan/{laporan_id}', [PelaporController::class, 'show'])->name('pelapor.show');

        // laporan bersama
        Route::get('/laporan_bersama', [PelaporController::class, 'laporanBersama'])->name('pelapor.laporan_bersama');
        Route::POST('/laporan/list_bersama', [PelaporController::class, 'listBersama'])->name('pelapor.list.bersama');
        Route::get('/laporan_bersama/{laporan_id}', [PelaporController::class, 'showBersama'])->name('pelapor.show.bersama');
        Route::POST('/laporan-bersama/{laporan_id}/dukung', [PelaporController::class, 'dukungLaporan'])->name('pelapor.dukungLaporan');

        // mengambil ruangan dan fasilitas untuk ajax chain
        Route::get('/get-ruangan/{lantai_id}', [PelaporController::class, 'getRuangan'])->name('pelapor.getRuangan');
        Route::get('/get-fasilitas/{ruangan_id}', [PelaporController::class, 'getFasilitas'])->name('pelapor.getFasilitas');

        //Feedback
        Route::get('/feedback', [PelaporController::class, 'feedback'])->name('pelapor.feedback');
        Route::get('/feedback/list', [PelaporController::class, 'feedbackList'])->name('pelapor.feedback.list');
        Route::get('/feedback/{tugas_id}', [PelaporController::class, 'feedbackShow'])->name('pelapor.feedback.show');
        Route::get('/feedback/form/{tugas_id}', [PelaporController::class, 'feedbackCreate'])->name('pelapor.feedback.create');
        Route::post('/feedback/store', [PelaporController::class, 'feedbackStore'])->name('pelapor.feedback.store');
    });
});

// Sarpras Management (role 5)
Route::middleware(['authorize:5'])->group(callback: function () {
    Route::get('/sarpras/dashboard', [SarprasController::class, 'index'])->name('sarpras.dashboard');
    Route::get('/sarpras/profile', [SarprasController::class, 'show'])->name('sarpras.profile.show');
    Route::get('/sarpras/profile/edit', [SarprasController::class, 'edit'])->name('sarpras.profile.edit');
    Route::put('/sarpras/profile', [SarprasController::class, 'update'])->name('sarpras.profile.update');
    Route::get('sarpras/verifikasi_laporan', [SarprasController::class, 'verifikasilaporan'])->name('sarpras.verifikasi');
    Route::get('sarpras/laporan/list_laporan', [SarprasController::class, 'listLaporan'])->name('sarpras.list.Laporan');
    Route::get('sarpras/laporan/{laporan_id}', [SarprasController::class, 'showLaporan'])->name('sarpras.show');
    Route::post('sarpras/laporan/{laporan_id}/approve', [SarprasController::class, 'approve'])->name('sarpras.approve');
    Route::post('sarpras/laporan/{laporan_id}/reject', [SarprasController::class, 'reject'])->name('sarpras.reject');
    Route::group(['prefix' => 'sarpras'], function () {
        Route::get('/dashboard', [SarprasController::class, 'index'])->name('sarpras.dashboard');
        Route::get('/profile', [SarprasController::class, 'show'])->name('sarpras.profile.show');
        Route::get('/profile/edit', [SarprasController::class, 'edit'])->name('sarpras.profile.edit');
        Route::put('/profile', [SarprasController::class, 'update'])->name('sarpras.profile.update');

        // Step-by-step SPK
        Route::group(['prefix' => 'sarpras/spk'], function (): void {
            Route::get('/spk_steps', [RekomendasiPerbaikan::class, 'showStepByStep'])->name('sarpras.spk.step_by_step');
        });

        // penugasan teknisi
        //pemeriksaan
        Route::get('/penugasan/pemeriksaan', [SarprasController::class, 'pemeriksaan'])->name('sarpras.pemeriksaan');
        Route::get('/penugasan/pemeriksaan/list', [SarprasController::class, 'pemeriksaanList'])->name('sarpras.pemeriksaan.list');
        Route::get('/penugasan/pemeriksaan/{tugas_id}', [SarprasController::class, 'pemeriksaanShow'])->name('sarpras.pemeriksaan.show');
        Route::get('/pemeriksaan/create', [SarprasController::class, 'pemeriksaanCreate'])->name('sarpras.pemeriksaan.create');
        Route::post('/penugasan/pemeriksaan/store', [SarprasController::class, 'pemeriksaanStore'])->name('sarpras.pemeriksaan.store');
        Route::get('/pemeriksaan/riwayat', [SarprasController::class, 'riwayatPemeriksaan'])->name('sarpras.riwayat.pemeriksaan');
        Route::get('/pemeriksaan/riwayat/list', [SarprasController::class, 'riwayatPemeriksaanList'])->name('sarpras.riwayat.pemeriksaan.list');
        //Perbaikan
        Route::get('/penugasan/perbaikan', [SarprasController::class, 'perbaikan'])->name('sarpras.perbaikan');
        Route::get('/penugasan/perbaikan/list', [SarprasController::class, 'perbaikanList'])->name('sarpras.perbaikan.list');
        Route::get('/penugasan/perbaikan/{tugas_id}', [SarprasController::class, 'perbaikanShow'])->name('sarpras.perbaikan.show');
        Route::get('/perbaikan/create', [SarprasController::class, 'perbaikanCreate'])->name('sarpras.perbaikan.create');
        Route::post('/penugasan/perbaikan/store', [SarprasController::class, 'perbaikanStore'])->name('sarpras.perbaikan.store');
        Route::get('/perbaikan/riwayat', [SarprasController::class, 'riwayatPerbaikan'])->name('sarpras.riwayat.perbaikan');
        Route::get('/perbaikan/riwayat/list', [SarprasController::class, 'riwayatPerbaikanList'])->name('sarpras.riwayat.perbaikan.list');

        Route::delete('/penugasan/{tugas_id}', [SarprasController::class, 'tugasDestroy'])->name('sarpras.penugasan.destroy');

        // ajax chain
        Route::get('get-ruangan/{lantai_id}', [SarprasController::class, 'getRuangan'])->name('sarpras.getRuangan');
        Route::get('get-fasilitas/{ruangan_id}', [SarprasController::class, 'getFasilitas'])->name('sarpras.getFasilitas');
        Route::get('get-fasilitas-laporan/{jenis_tugas}', [SarprasController::class, 'getFasilitasByJenisTugas'])->name('sarpras.getFasilitasByJenisTugas');
        Route::get('/get-data-pemeriksaan/{fasilitas_id}', [SarprasController::class, 'getDataPemeriksaan'])->name('sarpras.getPemeriksaan');

        // laporan
        Route::get('/laporan', [SarprasController::class, 'laporan'])->name('sarpras.laporan');
        Route::POST('/laporan/list', [SarprasController::class, 'list'])->name('sarpras.laporan.list');
        Route::get('/laporan/{laporan_id}', [SarprasController::class, 'showLaporan'])->name('sarpras.laporan.show');
        Route::post('/laporan/{laporan_id}/update-status', [SarprasController::class, 'updateStatusLaporan'])->name('sarpras.laporan.updateStatus');
        Route::get('/riwayat', [SarprasController::class, 'riwayatLaporan'])->name('sarpras.riwayat');
        Route::post('/riwayat/list', [SarprasController::class, 'riwayatList'])->name('sarpras.riwayat.list');
        Route::get('/riwayat/{laporan_id}', [SarprasController::class, 'showRiwayatLaporan'])->name('sarpras.riwayat.show');

        // spk
        Route::get('/spk', [RekomendasiPerbaikan::class, 'tampilkanSPK'])->name('sarpras.spk');
        Route::post('/perbarui-data', [RekomendasiPerbaikan::class, 'perbaruiData'])->name('sarpras.perbarui.data');
    });
});

// Rute untuk Teknisi (role 6)
Route::group(['prefix' => 'teknisi', 'middleware' => 'authorize:6'], function () {
    // Dashboard dan Profil Teknisi
    Route::get('/dashboard', [TeknisiController::class, 'dashboard'])->name('teknisi.dashboard');
    Route::get('/profile', [TeknisiController::class, 'showProfile'])->name('teknisi.profile.show');
    Route::get('/profile/edit', [TeknisiController::class, 'editProfile'])->name('teknisi.profile.edit');
    Route::put('/profile', [TeknisiController::class, 'updateProfile'])->name('teknisi.profile.update');
    // Manajemen Tugas Teknisi
    Route::get('/', [TeknisiController::class, 'index'])->name('teknisi.index');
    Route::get('/list', [TeknisiController::class, 'list'])->name('teknisi.list');
    Route::post('/', [TeknisiController::class, 'store'])->name('teknisi.store');
    Route::get('/{teknisi}/show', [TeknisiController::class, 'show'])->name('teknisi.show');

    // Riwayat Teknisi
    Route::get('/riwayat', [TeknisiController::class, 'riwayat'])->name('teknisi.riwayat');
    Route::get('/riwayat/list', [TeknisiController::class, 'riwayatList'])->name('teknisi.riwayat.list');
    Route::get('/laporan/{id}', [TeknisiController::class, 'showLaporan'])->name('teknisi.show_laporan');
    // Pemeriksaan
    Route::get('/pemeriksaan', [TeknisiController::class, 'pemeriksaan'])->name('teknisi.pemeriksaan');
    Route::get('/pemeriksaan/list', [TeknisiController::class, 'listPemeriksaan'])->name('teknisi.listpemeriksaan');
    Route::get('/pemeriksaan/{laporan_id}', [TeknisiController::class, 'showPemeriksaan'])->name('teknisi.showpemeriksaan');
    Route::get('/pemeriksaan/{id}/edit', [TeknisiController::class, 'editPemeriksaan'])->name('teknisi.editpemeriksaan');
    Route::put('/pemeriksaan/{id}/update', [TeknisiController::class, 'updatePemeriksaan'])->name('teknisi.updatepemeriksaan');
    // Perbaikan
    Route::get('/perbaikan', [TeknisiController::class, 'perbaikan'])->name('teknisi.perbaikan');
    Route::get('/perbaikan/list', [TeknisiController::class, 'listPerbaikan'])->name('teknisi.listperbaikan');
    Route::get('teknisi/perbaikan/{id}/edit', [TeknisiController::class, 'editPerbaikan'])->name('teknisi.editperbaikan');
    Route::put('teknisi/perbaikan/{id}', [TeknisiController::class, 'updatePerbaikan'])->name('teknisi.updateperbaikan');
    Route::get('/perbaikan/{laporan_id}', [TeknisiController::class, 'showPerbaikan'])->name('teknisi.showperbaikan');
});


Route::get('/python', [RekomendasiPerbaikan::class, 'hitungSPK'])->name('calculate');
