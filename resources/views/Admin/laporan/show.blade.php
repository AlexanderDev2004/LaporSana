@empty($laporan)
    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/laporan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Data Laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right col-3">ID laporan :</th>
                        <td class="col-9">{{ $laporan->laporan_id }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Pelapor :</th>
                        <td class="col-9">{{ $laporan->user->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Status :</th>
                        <td>
                            @php
                                $badgeClass = 'badge badge-secondary';
                                if ($laporan->status_id == 1)
                                    $badgeClass = 'badge badge-warning';
                                else if ($laporan->status_id == 2)
                                    $badgeClass = 'badge badge-danger';
                                else if ($laporan->status_id == 3)
                                    $badgeClass = 'badge badge-primary';
                                else if ($laporan->status_id == 4)
                                    $badgeClass = 'badge badge-success';
                            @endphp
                            <span class="{{ $badgeClass }}">{{ $laporan->status->status_nama ?? '-' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Tanggal Melapor :</th>
                        <td class="col-9">{{ $laporan->tanggal_lapor }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Jumlah Pelapor :</th>
                        <td class="col-9">{{ $laporan->jumlah_pelapor }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endempty