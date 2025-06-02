<div class="modal-content">
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th>ID Laporan</th>
                        <td>{{ $laporan->laporan_id }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Lapor</th>
                        <td>{{ \Carbon\Carbon::parse($laporan->tanggal_lapor)->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Pelapor</th>
                        <td>{{ $laporan->jumlah_pelapor }}</td>
                    </tr>
                    <tr>
                        <th>Fasilitas</th>
                        <td>{{ $laporan->details->first()->fasilitas->fasilitas_nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Ruangan</th>
                        <td>{{ $laporan->details->first()->fasilitas->ruangan->ruangan_nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Lantai</th>
                        <td>{{ $laporan->details->first()->fasilitas->ruangan->lantai->lantai_nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if ($laporan->status)
                                @switch($laporan->status_id)
                                    @case(1)
                                        <span class="badge badge-warning">Menunggu Verifikasi</span>
                                        @break
                                    @case(2)
                                        <span class="badge badge-danger">Ditolak</span>
                                        @break
                                    @case(3)
                                        <span class="badge badge-info">Diproses</span>
                                        @break
                                    @case(4)
                                        <span class="badge badge-success">Selesai</span>
                                        @break
                                @endswitch
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $laporan->details->first()->deskripsi ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                @if ($laporan->details->first()->foto_bukti)
                    <div class="form-group">
                        <label>Foto Bukti</label>
                        <div>
                            <img src="{{ asset('storage/' . $laporan->details->first()->foto_bukti) }}" alt="Foto Bukti" class="img-fluid" style="max-width: 100%; max-height: 300px;">
                        </div>
                    </div>
                @else
                    <div class="form-group">
                        <label>Foto Bukti</label>
                        <p>Tidak ada foto bukti.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Kembali</button>
    </div>
</div>