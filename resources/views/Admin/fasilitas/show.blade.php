@empty($fasilitas)
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
                <a href="{{ url('/fasilitas') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Detail Data Fasilitas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-bordered table-striped">
                        <tr>
                            <th class="text-right col-3">ID Fasilitas :</th>
                            <td class="col-9">{{ $fasilitas->fasilitas_id }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Ruangan</th>
                            <td class="col-9">{{ $fasilitas->ruangan->ruangan_nama }}</td>
                        </tr>
                           <tr>
                            <th class="text-right col-3">Lantai</th>
                            <td class="col-9">{{ $fasilitas->ruangan->lantai->lantai_nama }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Kode fasilitas :</th>
                            <td class="col-9">{{ $fasilitas->fasilitas_kode }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Nama :</th>
                            <td class="col-9">{{ $fasilitas->fasilitas_nama }}</td>
                        </tr>
                           <tr>
                            <th class="text-right col-3">Tingkat Urgensi :</th>
                            <td class="col-9">{{ $fasilitas->tingkat_urgensi }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
@endempty