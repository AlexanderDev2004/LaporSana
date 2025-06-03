<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <form action="{{ route('pelapor.store') }}" method="POST" id="form-tambah" enctype="multipart/form-data"> 
            @csrf
            <div class="modal-header"> 
                <h5 class="modal-title">Tambah Laporan Kerusakan</h5> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button> 
            </div> 
            <div class="modal-body"> 
                <div class="form-group"> 
                    <label>Lantai</label> 
                    <select name="lantai_id" id="lantai_id" class="form-control" required>
                        <option value="">- Pilih Lantai -</option> 
                        @foreach($lantai as $k) 
                            <option value="{{ $k->lantai_id }}">{{ $k->lantai_nama }}</option> 
                        @endforeach 
                    </select>
                </div>
                <div class="form-group"> 
                    <label>Ruangan</label> 
                    <select name="ruangan_id" id="ruangan_id" class="form-control" required>
                        <option value="">- Pilih Ruangan -</option> 
                        @foreach($ruangan as $k) 
                            <option value="{{ $k->ruangan_id }}">{{ $k->ruangan_nama }}</option> 
                        @endforeach 
                    </select>
                </div>
                <div class="form-group"> 
                    <label>Fasilitas</label> 
                    <select name="fasilitas_id" id="fasilitas_id" class="form-control" required>
                        <option value="">- Pilih Fasilitas -</option> 
                        @foreach($fasilitas as $k) 
                            <option value="{{ $k->fasilitas_id }}">{{ $k->fasilitas_nama }}</option> 
                        @endforeach 
                    </select>
                </div> 
                <div class="form-group"> 
                    <label>Foto Bukti</label> 
                    <input type="file" name="foto_bukti" id="foto_bukti" class="form-control-file"> 
                    <small id="error-foto_bukti" class="error-text form-text text-danger"></small> 
                </div> 
                <div class="form-group"> 
                    <label>Deskripsi</label> 
                    <textarea name="deskripsi" id="deskripsi" class="form-control" required rows="3"></textarea>
                    <small id="error-deskripsi" class="error-text form-text text-danger"></small> 
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
    $("#form-tambah").on('submit', function(e) {
        e.preventDefault();
        
        let form = this;
        let formData = new FormData(form);

        $.ajax({ 
            url: form.action, 
            type: form.method, 
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                if(response && response.status){
                    $('#myModal').modal('hide'); 
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Berhasil', 
                        text: response.message 
                    }).then(() => {
                        if (window.dataLaporan) {
                            window.dataLaporan.ajax.reload(null, false); 
                        } else {
                            location.reload();
                        }
                    });

                } else { 
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Terjadi Kesalahan', 
                        text: (response && response.message) ? response.message : 'Gagal menyimpan data.'
                    });
                }
            },
            error: function(xhr, status, error) {
                $('.error-text').text('');
                if(xhr.responseJSON && xhr.responseJSON.msgField) {
                    let errors = xhr.responseJSON.msgField;
                    $.each(errors, function(prefix, val) { 
                        $('#error-'+prefix).text(val[0]); 
                    });
                }
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Gagal Validasi', 
                    text: (xhr.responseJSON && xhr.responseJSON.message) || 'Periksa kembali isian form Anda.'
                });
            }
        }); 
    });
});
</script>