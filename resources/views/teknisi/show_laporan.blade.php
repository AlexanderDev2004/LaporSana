<!-- filepath: resources/views/teknisi/show_laporan.blade.php -->
@empty($laporan)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">Data tidak ditemukan</div>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Laporan {{ $laporan->laporan_id }}</h5>
            </div>
            <div class="modal-body">
                <b>Nama Pelapor:</b> {{ $laporan->user->name ?? '-' }}<br>
                <b>Status:</b>
                @php
                    $status = strtolower($laporan->status->status_nama ?? '-');
                    $badge = 'secondary';
                    switch ($status) {
                        case 'menunggu verifikasi':
                            $badge = 'warning';
                            break;
                        case 'ditolak':
                            $badge = 'danger';
                            break;
                        case 'diproses':
                            $badge = 'primary';
                            break;
                        case 'disetujui':
                            $badge = 'info';
                            break;
                        case 'selesai':
                            $badge = 'success';
                            break;
                        case 'selesai diperiksa':
                            $badge = 'success';
                            break;
                    }
                @endphp
                <span class="badge badge-{{ $badge }}">{{ $laporan->status->status_nama ?? '-' }}</span>
                <br>
                <b>Tanggal Lapor:</b> {{ $laporan->tanggal_lapor }}<br>
                <b>Jumlah Pelapor:</b> {{ $laporan->jumlah_pelapor }}<br>
                <hr>
                <h6>Detail Laporan:</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Fasilitas</th>
                            <th>Ruangan</th>
                            <th>Lantai</th>
                            <th>Deskripsi</th>
                            <th>Foto Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($laporan->details as $detail)
                            <tr>
                                <td>{{ $detail->fasilitas->fasilitas_nama ?? '-' }}</td>
                                <td>{{ $detail->fasilitas->ruangan->ruangan_nama ?? '-' }}</td>
                                <td>{{ $detail->fasilitas->ruangan->lantai->lantai_nama ?? '-' }}</td>
                                <td>{{ $detail->deskripsi }}</td>
                                <td>
                                    @if ($detail->foto_bukti)
                                        <img src="{{ asset('storage/' . $detail->foto_bukti) }}" width="80">
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
@endempty
