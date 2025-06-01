@extends('layouts.teknisi.template')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="card shadow-sm rounded">
        <div class="card-body">
            <table class="table align-middle table-hover">
                <thead>
                    <tr class="text-muted">
                        <th>Fasilitas</th>
                        <th>Tanggal Penugasan</th>
                        <th>Tanggal Selesai</th>
                        <th>Feedback Pengguna</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-semibold">Proyektor</div>
                            <div class="text-muted small">LIG1 Lantai 7</div>
                        </td>
                        <td>05-04-2025</td>
                        <td>06-04-2025</td>
                        <td>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <i class="far fa-star"></i>
                                <span class="text-muted small ms-1">3.5/5</span>
                            </div>
                        </td>
                        <td>
                           <a href="{{ route('teknisi.detail', 1) }}">Detail Laporan</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fw-semibold">Kipas Angin Kelas</div>
                            <div class="text-muted small">LIG1 Lantai 7</div>
                        </td>
                        <td>01-04-2025</td>
                        <td>02-04-2025</td>
                        <td>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span class="text-muted small ms-1">5/5</span>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('teknisi.detail', 2) }}" class="text-primary">Detail Laporan</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fw-semibold">Lampu Koridor</div>
                            <div class="text-muted small">Lantai 2</div>
                        </td>
                        <td>25-03-2025</td>
                        <td>26-03-2025</td>
                        <td>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <i class="far fa-star"></i>
                                <span class="text-muted small ms-1">3/5</span>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('teknisi.detail', 3) }}" class="text-primary">Detail Laporan</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
