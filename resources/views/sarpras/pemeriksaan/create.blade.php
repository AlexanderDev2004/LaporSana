<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <form action="{{ route('sarpras.pemeriksaan.store') }}" method="POST" id="form-tambah-pemeriksaan">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Form Pemeriksaan Baru</h5>
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

                <p class="font-weight-bold">Fasilitas dari Laporan Aktif</p>

                <div class="form-group">
                    <label>Fasilitas</label>
                    <select name="fasilitas_id" id="fasilitas_id" class="form-control" required>
                        <option value="">- Pilih Fasilitas -</option>
                        @foreach($fasilitasLaporan as $f)
                            <option value="{{ $f->fasilitas_id }}" data-laporan="{{ $f->laporan_id }}">
                                {{ $f->fasilitas_nama }} ({{ $f->ruangan_nama }} - {{ $f->lantai_nama }})
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="laporan_id" id="laporan_id">
                </div>

                <div class="form-group">
                    <label>Deskripsi Tambahan (Opsional)</label>
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
    // Ambil nilai laporan_id dari option yang dipilih
    $('#fasilitas_id').on('change', function () {
        let laporanId = $(this).find(':selected').data('laporan');
        $('#laporan_id').val(laporanId);
    });

    // Submit form dengan Ajax
    $('#form-tambah-pemeriksaan').on('submit', function (e) {
        e.preventDefault();

        let form = this;
        let formData = $(form).serialize();

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    $('#myModal').modal('hide');
                    Swal.fire('Berhasil!', response.message, 'success')
                        .then(() => {
                            $('#table_tugas').DataTable().ajax.reload(null, false);
                        });
                } else {
                    Swal.fire('Gagal!', response.message || 'Tugas gagal ditambahkan.', 'error');
                }
            },
            error: function (xhr) {
                let msg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON?.errors) {
                    msg = '';
                    $.each(xhr.responseJSON.errors, function (key, val) {
                        msg += val[0] + '\n';
                    });
                } else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Validasi Gagal!', msg, 'error');
            }
        });
    });
});
</script>
