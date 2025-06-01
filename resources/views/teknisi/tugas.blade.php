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
                        <th>Status</th>
                        <th>Detail</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-semibold">WC Toilet Pria</div>
                            <div class="text-muted small">Lantai 8</div>
                        </td>
                        <td>14-04-2025</td>
                        <td><span class="badge bg-warning text-dark">Diproses</span></td>
                        <td><a href="#" class="text-primary">Detail Laporan</a></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-success me-1"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fw-semibold">Proyektor Ruang Rapat</div>
                            <div class="text-muted small">Lantai 6</div>
                        </td>
                        <td>10-04-2025</td>
                        <td><span class="badge bg-success">Selesai</span></td>
                        <td><a href="#" class="text-primary">Detail Laporan</a></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-success me-1"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="fw-semibold">AC </div>
                            <div class="text-muted small">Lantai 6</div>
                        </td>
                        <td>09-04-2025</td>
                        <td><span class="badge bg-secondary">Menunggu Diverifikasi</span></td>
                        <td><a href="#" class="text-primary">Detail Laporan</a></td>
                        <td>
                            <a href="#" class="btn btn-sm btn-success me-1"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
