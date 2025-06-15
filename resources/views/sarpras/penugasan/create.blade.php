<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <form action="{{ route('sarpras.penugasan.store') }}" method="POST" id="form-tambah-tugas">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Form Penugasan Baru</h5>
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
                    <label>Jenis Tugas</label>
                    <select name="tugas_jenis" id="tugas_jenis" class="form-control" required>
                        <option value="">- Pilih Jenis Tugas -</option>
                        <option value="Pemeriksaan">Pemeriksaan</option>
                        <option value="Perbaikan">Perbaikan</option>
                    </select>
                </div>

                <hr>
                <p class="font-weight-bold">Fasilitas dari Laporan Aktif</p>

                <div class="form-group">
                    <label>Fasilitas</label>
                    <select name="fasilitas_id" id="fasilitas_id" class="form-control" required disabled>
                        <option value="">- Pilih Jenis Tugas Dulu -</option>
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
$(document).ready(function() {
    // Load fasilitas berdasarkan jenis tugas
    $('#tugas_jenis').on('change', function () {
        let jenisTugas = $(this).val();
        let fasilitasSelect = $('#fasilitas_id');

        fasilitasSelect.empty().append('<option value="">- Memuat data... -</option>').prop('disabled', true);
        $('#laporan_id').val('');

        if (jenisTugas) {
            $.get('/sarpras/get-fasilitas-laporan/' + jenisTugas, function (data) {
                fasilitasSelect.empty().append('<option value="">- Pilih Fasilitas -</option>');

                if (data.length === 0) {
                    fasilitasSelect.append('<option disabled>Tidak ada data tersedia</option>');
                } else {
                    $.each(data, function (i, item) {
                        fasilitasSelect.append(
                            '<option value="' + item.fasilitas_id + '" data-laporan="' + item.laporan_id + '">' +
                            item.fasilitas_nama + ' (' + item.ruangan_nama + ' - ' + item.lantai_nama + ')' +
                            '</option>'
                        );
                    });
                }

                fasilitasSelect.prop('disabled', false);
            });
        } else {
            fasilitasSelect.empty().append('<option value="">- Pilih Jenis Tugas Dulu -</option>').prop('disabled', true);
        }
    });

    // Simpan laporan_id dari fasilitas terpilih
    $('#fasilitas_id').on('change', function () {
        let laporanId = $(this).find(':selected').data('laporan');
        $('#laporan_id').val(laporanId);
    });

    // Submit form
    $("#form-tambah-tugas").on('submit', function(e) {
        e.preventDefault();

        let form = this;
        let formData = $(form).serialize();

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#myModal').modal('hide');
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Berhasil', 
                        text: response.message 
                    }).then(() => {
                        $('#table_tugas').DataTable().ajax.reload(null, false);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Tugas gagal ditambahkan.'
                    });
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = '';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorMsg += value[0] + '\n';
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Gagal Validasi', 
                    text: errorMsg
                });
            }
        });
    });
});
</script>
