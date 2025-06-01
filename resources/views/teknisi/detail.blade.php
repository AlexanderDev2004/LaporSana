@extends('layouts.teknisi.template')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm rounded">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Detail Laporan</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Fasilitas:</strong> {{ $laporan->fasilitas_nama }} <br>
                <small class="text-muted">{{ $laporan->fasilitas_lokasi }}</small>
            </div>

            <div class="mb-3">
                <strong>Tanggal Penugasan:</strong> 
                {{ \Carbon\Carbon::parse($laporan->tanggal_penugasan)->format('d-m-Y') }} <br>
                <strong>Tanggal Selesai:</strong> 
                {{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->format('d-m-Y') }}
            </div>

            <div class="mb-3">
                <strong>Deskripsi Kerusakan:</strong>
                <p>{{ $laporan->deskripsi }}</p>
            </div>

            <div class="mb-3">
                <strong>Feedback Pengguna:</strong><br>
                @php $rating = $laporan->feedback_rating; @endphp
                <div class="text-warning" style="font-size: 1.3rem;">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($rating >= $i)
                            <i class="fas fa-star"></i>
                        @elseif ($rating >= $i - 0.5)
                            <i class="fas fa-star-half-alt"></i>
                        @else
                            <i class="far fa-star"></i>
                        @endif
                    @endfor
                    <span class="text-muted ms-2">{{ $rating }}/5</span>
                </div>
                <div class="mt-2 fst-italic">"{{ $laporan->feedback_komentar }}"</div>
            </div>

            <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection
