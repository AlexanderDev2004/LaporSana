@extends('layouts.admin.template')

@section('content')

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Halo <span> Selamat Datang</span>, {{ auth()->user()->name }} 👋</h3>
    <div class="card-tools"></div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <p>Anda masuk sebagai: <strong>{{ auth()->user()->role->roles_nama }}</strong></p>
      </div>
      <div class="col-md-6 text-start">
        <img src="{{ auth()->user()->avatar ? url('storage/' . auth()->user()->avatar) : asset('LaporSana/dist/img/user2-160x160.jpg') }}" alt="Profile Picture" class="img-fluid rounded-circle" style="max-width: 300px;">
      </div>
    </div>
    <div class="row mt-4">
      <div class="col-md-12">
        <h5>Informasi Sistem</h5>
        <ul>
          <li>Versi Sistem: <strong>{{ config('app.version') }}</strong></li>
          <li>Waktu Server: <strong>{{ now()->format('d F Y H:i:s') }}</strong></li>
          {{-- <li>Jumlah Pengguna Terdaftar: <strong>{{ $userCount }}</strong></li>
          <li>Jumlah Laporan Terkirim: <strong>{{ $reportCount }}</strong></li> --}}
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection
