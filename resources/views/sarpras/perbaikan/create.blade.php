<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <form action="{{ route('sarpras.perbaikan.store') }}" method="POST" id="form-tambah-tugas">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Form Penugasan Perbaikan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Teknisi</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">- Pilih Teknisi -</option>
                        @foreach($teknisi as $t)
                            <option value="{{ $t->user_id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Fasilitas (Sudah di-SPK)</label>
                    <select name="fasilitas_id" id="fasilitas_id" class="form-control" required>
                        <option value="">- Pilih Fasilitas -</option>
                        @foreach($fasilitasLaporan as $f)
                            <option value="{{ $f->fasilitas_id }}">
                                {{ $f->fasilitas->fasilitas_nama ?? '-' }}
                                ({{ $f->fasilitas->ruangan->ruangan_nama ?? '-' }} - {{ $f->fasilitas->ruangan->lantai->lantai_nama ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="laporan_id" id="laporan_id">
                </div>

                <div class="form-group">
                    <label>Tingkat Kerusakan</label>
                    <input type="number" name="tingkat_kerusakan" id="tingkat_kerusakan" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label>Biaya Perbaikan</label>
                    <input type="number" name="biaya_perbaikan" id="biaya_perbaikan" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label>Deskripsi Tambahan</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function () {
    // Ambil laporan_id dari fasilitas yang dipilih
    $('#fasilitas_id').on('change', function () {
        const laporanId = $(this).find(':selected').data('laporan');
        const fasilitasId = $(this).val();
        $('#laporan_id').val(laporanId);

        // Ambil data dari pemeriksaan
        if (fasilitasId) {
            $.get('/sarpras/get-data-pemeriksaan/' + fasilitasId, function (data) {
                if (data) {
                    $('#tingkat_kerusakan').val(data.tingkat_kerusakan);
                    $('#biaya_perbaikan').val(data.biaya_perbaikan);
                } else {
                    $('#tingkat_kerusakan').val('');
                    $('#biaya_perbaikan').val('');
                }
            });
        }
    });

    // Submit via AJAX
    $('#form-tambah-tugas').on('submit', function (e) {
        e.preventDefault();
        let form = this;
        let formData = $(form).serialize();

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (res) {
                if (res.status) {
                    $('#myModal').modal('hide');
                    Swal.fire('Berhasil!', res.message, 'success')
                        .then(() => $('#table_tugas').DataTable().ajax.reload(null, false));
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            },
            error: function (xhr) {
                let msg = 'Terjadi kesalahan.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).join('\n');
                }
                Swal.fire('Error!', msg, 'error');
            }
        });
    });
});
</script>
