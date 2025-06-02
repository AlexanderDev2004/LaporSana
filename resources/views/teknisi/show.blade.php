@empty($tugas)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
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
                <a href="{{ route('teknisi.show') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                     <tr>
                        <th width="25%">ID Tugas</th>
                        <td>{{ $tugas->tugas_id }}</td>
                    </tr>
                    <tr>
                        <th width="25%">Nama Teknisi:</th>
                        <td>{{ $tugas->user->name}}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>{{ $tugas->status->status_nama}}</td>
                    </tr>
                    <tr>
                        <th>Jenis Tugas:</th>
                        <td>{{ $tugas->tugas_jenis }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pengerjaan:</th>
                        <td>{{ $tugas->tugas_mulai}}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Penyelesaian:</th>
                        <td>{{ $tugas->tugas_selesai }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-primary">Tutup</button>
            </div>
        </div>
    </div>
@endempty