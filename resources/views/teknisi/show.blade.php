@empty($tugas)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
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
                <h5 class="modal-title">Detail Data Tugas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <table class="table table-sm table-bordered table-striped mb-4">
                    <tr>
                        <th class="text-right col-3">ID Detail Tugas :</th>
                        <td class="col-9">{{ $tugas->detail_id }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Fasilitas :</th>
                        <td class="col-9">{{ $tugas->fasilitas->fasilitas_nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Gambar Bukti :</th>
                        <td>
                            @if ($tugas->tugas_image)
                                <img src="{{ asset('storage/tugas/' . $tugas->tugas_image) }}" width="100"
                                    alt="Gambar">
                            @else
                                <em>Tidak ada gambar</em>
                            @endif
                        </td>
                    </tr>
                     <tr>
                        <th class="text-right col-3">Deskripsi Kerusakan:</th>
                        <td class="col-9">{{ $tugas->deskripsi }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Tingkat Kerusakan:</th>
                        <td class="col-9">{{ $tugas->tingkat_kerusakan}}</td>
                    </tr>
                     <tr>
                        <th class="text-right col-3">Biaya Perbaikan:</th>
                        <td class="col-9">{{ $tugas->biaya_perbaikan}}</td>
                    </tr>
                </table>
                <div class="text-right mt-4">
                        <a href="{{ url('/teknisi') }}" class="btn btn-secondary">Tutup</a>
                    </div>
            </div>
        </div>
    </div>
@endempty
