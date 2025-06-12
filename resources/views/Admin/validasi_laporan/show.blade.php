@extends('layouts.admin.template')

@section('content')
<div class="container-fluid">
    @if($laporan)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detail Laporan #{{ $laporan->laporan_id }}</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Pelapor:</strong> {{ $laporan->user->name ?? 'N/A' }}</p>
                        <p><strong>Jumlah Pelapor:</strong> {{ $laporan->jumlah_pelapor }}</p>
                        <p><strong>Tanggal Lapor:</strong> {{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d M Y H:i') }}</p>
                        <p><strong>Status:</strong>
                            <span class="badge badge-{{ $laporan->status_id == 1 ? 'warning' : ($laporan->status_id == 2 ? 'success' : 'danger') }}">
                                {{ $laporan->status->status_nama ?? 'N/A' }}
                            </span>
                        </p>
                    </div>
                </div>

                <hr>

                <h5>Detail Fasilitas:</h5>
                @forelse($laporan->details as $detail)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6>{{ $detail->fasilitas->fasilitas_nama ?? 'Fasilitas Tidak Diketahui' }}</h6>
                            <p><strong>Lokasi:</strong>
                                {{ $detail->fasilitas->ruangan->nama_ruangan ?? 'N/A' }}
                                (Lantai {{ optional(optional($detail->fasilitas->ruangan)->lantai)->nama_lantai ?? 'N/A' }})
                            </p>
                            <p><strong>Deskripsi:</strong> {{ $detail->deskripsi }}</p>
                            @if($detail->foto_bukti)
                                <img src="{{ asset('storage/'.$detail->foto_bukti) }}" class="img-fluid" style="max-height: 200px;">
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Tidak ada detail fasilitas pada laporan ini.</p>
                @endforelse
            </div>
        </div>
    @else
        <div class="alert alert-danger">Laporan tidak ditemukan.</div>
    @endif
</div>
@endsection
