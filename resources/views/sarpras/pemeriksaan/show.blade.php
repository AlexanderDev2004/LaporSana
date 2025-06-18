@php
    $detail = $pemeriksaan->details->first();
@endphp

<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Detail Pemeriksaan #{{ $pemeriksaan->tugas_id }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            @if ($detail)
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered table-sm">
                            <tr>
                                <th style="width: 35%;">ID Tugas</th>
                                <td>{{ $pemeriksaan->tugas_id }}</td>
                            </tr>
                            <tr>
                                <th>Nama Teknisi</th>
                                <td>{{ $pemeriksaan->user->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Tugas</th>
                                <td>{{ $pemeriksaan->tugas_jenis }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Mulai</th>
                                <td>
                                    {{ \Carbon\Carbon::parse($pemeriksaan->tugas_mulai)->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Selesai</th>
                                <td>
                                    @if ($pemeriksaan->tugas_selesai)
                                        {{ \Carbon\Carbon::parse($pemeriksaan->tugas_selesai)->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB
                                    @else
                                        <span class="text-muted">Belum selesai</span>
                                    @endif
                                </td>
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
                                <td>{{ $detail->tingkat_kerusakan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Biaya Perbaikan</th>
                                <td>Rp {{ number_format($detail->biaya_perbaikan, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @switch($pemeriksaan->status_id)
                                        @case(1) <span class="badge badge-warning">Menunggu Verifikasi</span> @break
                                        @case(2) <span class="badge badge-danger">Ditolak</span> @break
                                        @case(3) <span class="badge badge-info">Diproses</span> @break
                                        @case(4) <span class="badge badge-success">Selesai</span> @break
                                         @case(6) <span class="badge badge-success">Selesai diperiksa</span> @break
                                        @default <span class="badge badge-secondary">Tidak Diketahui</span>
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td>{!! nl2br(e($detail->deskripsi ?? '-')) !!}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        @if ($detail->tugas_image)
                            <div class="form-group">
                                <label>Foto Bukti</label>
                                <div>
                                    <a href="{{ asset('storage/' . $detail->tugas_image) }}" target="_blank" title="Klik untuk melihat ukuran penuh">
                                        <img src="{{ asset('storage/' . $detail->tugas_image) }}" alt="Foto Bukti" class="img-fluid img-thumbnail" style="max-width: 100%; max-height: 300px; cursor:pointer;">
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
                    </div>
                </div>
            @else
                <div class="alert alert-danger text-center">
                    Detail pemeriksaan tidak ditemukan.
                </div>
            @endif
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">Tutup</button>
        </div>
    </div>
</div>