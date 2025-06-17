@php
    $detail = $laporan->details->first();
@endphp

<div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="detailLaporanModalLabel">Detail Laporan #{{ $laporan->laporan_id }}</h5>
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
                                <th style="width: 35%;">ID Laporan</th>
                                <td>{{ $laporan->laporan_id }}</td>
                            </tr>
                            <tr>
                                <th>Nama Pelapor</th>
                                <td>{{ $laporan->user->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Lapor</th>
                                <td>{{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB</td>
                            </tr>
                            <tr>
                                <th>Jumlah Pelapor</th>
                                <td>{{ $laporan->jumlah_pelapor }} Orang</td>
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
                                <th>Status</th>
                                <td>
                                    @if ($laporan->status)
                                        @switch($laporan->status_id)
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
                        @if ($detail->foto_bukti)
                            <div class="form-group">
                                <label>Foto Bukti</label>
                                <div>
                                    <a href="{{ asset('storage/' . $detail->foto_bukti) }}" target="_blank" title="Klik untuk melihat ukuran penuh">
                                        <img src="{{ asset('storage/' . $detail->foto_bukti) }}" alt="Foto Bukti Kerusakan" class="img-fluid img-thumbnail" style="max-width: 100%; max-height: 300px; cursor:pointer;">
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

                <hr>
                <h5 class="mt-3">Daftar Pendukung Laporan</h5>
                @if ($laporan->dukungan->isNotEmpty())
                    <table class="table table-bordered table-sm mt-2">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Role</th>
                                <th>Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporan->dukungan as $i => $dukungan)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $dukungan->user->name ?? '-' }}</td>
                                    <td>{{ $dukungan->user->role->roles_nama ?? '-' }}</td>
                                    <td>{{ $dukungan->poin_roles }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-muted font-italic">
                        Belum ada user lain yang ikut melaporkan kerusakan ini.
                    </div>
                @endif
            @else
                <div class="alert alert-danger text-center">
                    Detail untuk laporan ini tidak ditemukan.
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">Tutup</button>
        </div>
    </div>
</div>
