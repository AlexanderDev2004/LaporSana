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
                <a href="{{ url('/teknisi') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail Data Tugas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-bordered table-striped">
                        <tr>
                            <th class="text-right col-3">ID Tugas :</th>
                            <td class="col-9">{{ $tugas->tugas_id }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Nama Teknisi :</th>
                            <td class="col-9">{{ $tugas->user->name}}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Status :</th>
                            <td class="col-9">{{ $tugas->status->status_nama }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Jenis Tugas :</th>
                            <td class="col-9">{{ $tugas->tugas_jenis }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Tanggal Pengerjaan :</th>
                            <td class="col-9">{{ $tugas->tugas_mulai }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Tanggal Penyelesaian :</th>
                            <td class="col-9">{{ $tugas->tugas_selesai }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
@endempty