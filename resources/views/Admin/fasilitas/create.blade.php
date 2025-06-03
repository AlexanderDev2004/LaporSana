<form action="{{ route('admin.fasilitas.store') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Fasilitas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                 <div class="form-group">
                    <label>Ruangan Fasilitas</label>
                    <select name="ruangan_id" id="ruangan_id" class="form-control" required>
                        <option value="">- Pilih Ruangan -</option>
                        @foreach ($ruangan as $r)
                            <option value="{{ $r->ruangan_id }}">{{ $r->ruangan_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-ruangan_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Kode fasilitas</label>
                    <input type="text" name="fasilitas_kode" id="fasilitas_kode" class="form-control" required>
                    <small id="error-fasilitas_kode" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Nama fasilitas</label>
                    <input type="text" name="fasilitas_nama" id="fasilitas_nama" class="form-control" required>
                    <small id="error-fasilitas_nama" class="error-text form-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
<script>
   $(document).ready(function() {
        $("#form-tambah").validate({
            rules: {
                ruangan_id: {required: true},
                fasilitas_kode: {required: true },
                fasilitas_nama: {required: true, minlength: 3, maxlength: 50},
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            dataFasilitas.ajax.reload();
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-'+prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>