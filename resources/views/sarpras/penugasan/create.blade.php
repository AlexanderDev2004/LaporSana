<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <form action="{{ route('sarpras.penugasan.store') }}" method="POST" id="form-tambah-tugas">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Form Penugasan Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                {{-- Field spesifik untuk Penugasan --}}
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
                
                {{-- Chained Dropdown untuk Lokasi, mirip dengan form Pelapor --}}
                <p class="font-weight-bold">Detail Lokasi & Fasilitas</p>
                <div class="form-group">
                    <label>Lantai</label>
                    <select name="lantai_id" id="lantai_id" class="form-control" required>
                        <option value="">- Pilih Lantai -</option>
                        @foreach($lantai as $l)
                            <option value="{{ $l->lantai_id }}">{{ $l->lantai_nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Ruangan</label>
                    <select name="ruangan_id" id="ruangan_id" class="form-control" required disabled>
                        <option value="">- Pilih Lantai Terlebih Dahulu -</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Fasilitas</label>
                    <select name="fasilitas_id" id="fasilitas_id" class="form-control" required disabled>
                        <option value="">- Pilih Ruangan Terlebih Dahulu -</option>
                    </select>
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
    // Event listener untuk chained dropdown Lantai -> Ruangan
    $('#lantai_id').on('change', function() {
        let lantaiId = $(this).val();
        let ruanganSelect = $('#ruangan_id');
        let fasilitasSelect = $('#fasilitas_id');

        ruanganSelect.empty().append('<option value="">- Memuat...</option>').prop('disabled', true);
        fasilitasSelect.empty().append('<option value="">- Pilih Ruangan Dulu -</option>').prop('disabled', true);

        if (lantaiId) {
            $.get('{{ url("/sarpras/get-ruangan") }}/' + lantaiId, function(data) {
                ruanganSelect.empty().append('<option value="">- Pilih Ruangan -</option>').prop('disabled', false);
                $.each(data, function(key, value) {
                    ruanganSelect.append('<option value="' + value.ruangan_id + '">' + value.ruangan_nama + '</option>');
                });
            });
        } else {
            ruanganSelect.empty().append('<option value="">- Pilih Lantai Dulu -</option>').prop('disabled', true);
        }
    });

    // Event listener untuk chained dropdown Ruangan -> Fasilitas
    $('#ruangan_id').on('change', function() {
        let ruanganId = $(this).val();
        let fasilitasSelect = $('#fasilitas_id');
        
        fasilitasSelect.empty().append('<option value="">- Memuat...</option>').prop('disabled', true);

        if (ruanganId) {
            $.get('{{ url("/sarpras/get-fasilitas") }}/' + ruanganId, function(data) {
                fasilitasSelect.empty().append('<option value="">- Pilih Fasilitas -</option>').prop('disabled', false);
                $.each(data, function(key, value) {
                    fasilitasSelect.append('<option value="' + value.fasilitas_id + '">' + value.fasilitas_nama + '</option>');
                });
            });
        } else {
            fasilitasSelect.empty().append('<option value="">- Pilih Ruangan Dulu -</option>').prop('disabled', true);
        }
    });

    // Event listener untuk submit form
    $("#form-tambah-tugas").on('submit', function(e) {
        e.preventDefault();
        
        let form = this;
        // Karena form ini tidak ada file upload, kita bisa pakai .serialize()
        let formData = $(form).serialize();

        $.ajax({ 
            url: $(form).attr('action'), 
            type: 'POST', 
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.status){
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
                        title: 'Terjadi Kesalahan', 
                        text: (response && response.message) ? response.message : 'Gagal menyimpan data.'
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