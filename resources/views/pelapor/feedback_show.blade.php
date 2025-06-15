@php
    $detail = $tugas->details->first();
    $riwayat = \App\Models\RiwayatPerbaikan::where('tugas_id', $tugas->tugas_id)->first();
@endphp

<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Detail Ulasan #{{ $tugas->tugas_id }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
        <div class="modal-body">
            @if ($detail)
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-sm">
                            <tr>
                                <th style="width: 35%;">ID Tugas</th>
                                <td>{{ $tugas->tugas_id }}</td>
                            </tr>
                            <tr>
                                <th>Nama Teknisi</th>
                                <td>{{ $tugas->user->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Tugas</th>
                                <td>{{ $tugas->tugas_jenis }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Mulai</th>
                                <td>{{ \Carbon\Carbon::parse($tugas->tugas_mulai)->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB</td>
                            </tr>
                            <tr>
                                <th>Tanggal Selesai</th>
                                <td>{{ \Carbon\Carbon::parse($tugas->tugas_selesai)->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB</td>
                            </tr>
                            <tr>
                                <th>Fasilitas</th>
                                <td>{{ $detail->fasilitas->fasilitas_nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Ruangan</th>
                                <td>{{ $detail->fasilitas->ruangan->ruangan_nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Lantai</th>
                                <td>{{ $detail->fasilitas->ruangan->lantai->lantai_nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tingkat Kerusakan</th>
                                <td>{{ $detail->tingkat_kerusakan }}</td>
                            </tr>
                            <tr>
                                <th>Biaya Perbaikan</th>
                                <td>{{ $detail->biaya_perbaikan }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if ($tugas->status)
                                        @switch($tugas->status_id)
                                            @case(1) <span class="badge badge-warning">Menunggu Verifikasi</span> @break
                                            @case(2) <span class="badge badge-danger">Ditolak</span> @break
                                            @case(3) <span class="badge badge-info">Diproses</span> @break
                                            @case(4) <span class="badge badge-success">Selesai</span> @break
                                            @default <span class="badge badge-secondary">Tidak Diketahui</span>
                                        @endswitch
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{!! nl2br(e($detail->deskripsi ?? '-')) !!}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        {{-- Gambar --}}
                        @if ($detail->tugas_image)
                            <div class="form-group">
                                <label>Foto Bukti</label>
                                <div>
                                    <a href="{{ asset('storage/' . $detail->tugas_image) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $detail->tugas_image) }}" class="img-fluid img-thumbnail" style="max-height: 300px; cursor:pointer;">
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="form-group">
                                <label>Foto Bukti</label>
                                <div class="text-center text-muted border rounded p-3">
                                    <i class="fas fa-image fa-2x mb-2"></i>
                                    <p class="mb-0">Tidak ada foto bukti.</p>
                                </div>
                            </div>
                        @endif

                        {{-- Feedback --}}
                        <div class="form-group mt-4">
                            <label>Feedback</label>
                            @if ($riwayat)
                                <div class="border rounded p-3">
                                    <p><strong>Rating:</strong>
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $riwayat->rating)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                        <span class="ml-2">({{ $riwayat->rating }} / 5)</span>
                                    </p>
                                    <p><strong>Ulasan:</strong> {{ $riwayat->ulasan ?: 'Tidak ada ulasan.' }}</p>
                                    <p class="text-muted"><small>Dikirim pada {{ $riwayat->created_at->format('d M Y H:i') }}</small></p>
                                </div>
                            @else
                                <div class="text-center text-muted border rounded p-3">
                                    <i class="fas fa-star-half-alt fa-2x mb-2"></i>
                                    <p class="mb-0">Belum ada feedback dari pelapor.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-danger text-center">
                    Detail untuk tugas ini tidak ditemukan.
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">Tutup</button>
        </div>
    </div>
</div>
