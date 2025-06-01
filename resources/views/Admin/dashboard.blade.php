@extends('layouts.admin.template')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                Halo <span class="fw-bold">Selamat Datang</span>, {{ auth()->user()->name }} 👋
            </h3>
        </div>

        <div class="card-body">
            <div class="row align-items-center mb-4">
                <div class="col-md-8">
                    <p class="mb-2">
                        Anda masuk sebagai:
                        <span class="badge badge-success px-3 py-2">
                            {{ auth()->user()->role->roles_nama }}
                        </span>
                    </p>
                </div>
                <div class="col-md-4 text-md-end text-center">
                    <img src="{{ auth()->user()->avatar ? url('storage/' . auth()->user()->avatar) : asset('LaporSana/dist/img/user2-160x160.jpg') }}"
                        alt="Profile Picture" class="rounded-circle border border-primary shadow-sm"
                        style="width: 120px; height: 120px; object-fit: cover;">
                </div>
            </div>

            <div class="mb-4">
                <h4 class="fw-bold text-dark">Tentang LaporSana</h4>
                <p class="text-muted">
                    <strong>LaporSana</strong> adalah sistem pelaporan fasilitas digital yang memudahkan pengguna dalam menyampaikan laporan
                    terkait kerusakan, gangguan, atau kebutuhan perbaikan terhadap fasilitas yang tersedia. Sistem ini
                    dirancang agar setiap laporan yang masuk dapat segera ditindaklanjuti oleh pihak yang berwenang,
                    sehingga fasilitas dapat tetap berfungsi dengan baik dan memberikan kenyamanan bagi pengguna.
                </p>
            </div>

            <div class="mt-4">
                <h5 class="fw-bold">Informasi Sistem</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Waktu Server:
                        <span class="text-info">{{ now()->format('d F Y H:i:s') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Jumlah Pengguna:
                        <span class="text-danger">{{ \App\Models\UserModel::count() }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
