@extends('layouts.pelapor.template')

@section('content')

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Halo, ini dashboard LaporSana!</h3>
    <div class="card-tools"></div>
  </div>

  <div class="card-body">
    <p>Selamat datang! <strong>{{ Auth::user()->name }}</strong>,</p>


    <!-- Deskripsi dan Panduan -->
    <div class="card mt-4">
      <div class="card-body">
        <p>
          <strong>Laporsana</strong> adalah sistem pelaporan fasilitas berbasis digital yang memudahkan pengguna dalam menyampaikan laporan terkait kerusakan, gangguan, atau kebutuhan perbaikan terhadap fasilitas yang tersedia. Sistem ini dirancang agar setiap laporan yang masuk dapat segera ditindaklanjuti oleh pihak terkait secara cepat, transparan, dan terorganisir.
        </p>
        <p>
          Melalui Laporsana, Anda tidak perlu lagi melapor secara manual atau mendatangi petugas secara langsung. Cukup beberapa langkah mudah, laporan Anda langsung tercatat dalam sistem dan diproses sesuai prioritas. Mari bersama menciptakan lingkungan yang lebih aman, nyaman, dan tertata melalui partisipasi aktif dalam pelaporan fasilitas.
        </p>

        <p><strong>📌 Panduan Singkat Pengaduan:</strong></p>
        <ol>
          <li>Masuk ke sistem Laporsana menggunakan akun yang telah disediakan.</li>
          <li>Pilih menu <strong>Buat Laporan Baru</strong>.</li>
          <li>Isi formulir pelaporan:
            <ul>
              <li>Jenis fasilitas yang dilaporkan</li>
              <li>Lokasi kejadian</li>
              <li>Deskripsi singkat masalah</li>
              <li>(Opsional) Unggah foto pendukung</li>
            </ul>
          </li>
          <li>Klik <strong>Kirim Laporan</strong>.</li>
          <li>Pantau status laporan Anda di menu <strong>Riwayat Laporan</strong>.</li>
          <li>Berikan hasil fasilitas yang telah diperbaiki di menu <strong>Feedback</strong>.</li>
        </ol>

        <p>
          Butuh panduan lebih lengkap?
          <a href="#">Buku Panduan Laporsana di sini</a>
        </p>
      </div>
    </div>
  </div>
</div>

@endsection
