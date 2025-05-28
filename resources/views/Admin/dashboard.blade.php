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

<script>
    const labels = Utils.months({count: 7});
const data = {
  labels: labels,
  datasets: [{
    label: 'My First Dataset',
    data: [65, 59, 80, 81, 56, 55, 40],
    backgroundColor: [
      'rgba(255, 99, 132, 0.2)',
      'rgba(255, 159, 64, 0.2)',
      'rgba(255, 205, 86, 0.2)',
      'rgba(75, 192, 192, 0.2)',
      'rgba(54, 162, 235, 0.2)',
      'rgba(153, 102, 255, 0.2)',
      'rgba(201, 203, 207, 0.2)'
    ],
    borderColor: [
      'rgb(255, 99, 132)',
      'rgb(255, 159, 64)',
      'rgb(255, 205, 86)',
      'rgb(75, 192, 192)',
      'rgb(54, 162, 235)',
      'rgb(153, 102, 255)',
      'rgb(201, 203, 207)'
    ],
    borderWidth: 1
  }]
};
</script>
