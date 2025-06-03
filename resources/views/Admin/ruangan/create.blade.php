<form action="{{ route('admin.ruangan.store') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Ruangan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                 <div class="form-group">
                    <label>Lantai Ruangan</label>
                    <select name="lantai_id" id="lantai_id" class="form-control" required>
                        <option value="">- Pilih Lantai -</option>
                        @foreach ($lantai as $l)
                            <option value="{{ $l->lantai_id }}">{{ $l->lantai_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-lantai_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Kode Ruangan</label>
                    <input type="text" name="ruangan_kode" id="ruangan_kode" class="form-control" required>
                    <small id="error-ruangan_kode" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Nama Ruangan</label>
                    <input type="text" name="ruangan_nama" id="ruangan_nama" class="form-control" required>
                    <small id="error-ruangan_nama" class="error-text form-text text-danger"></small>
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
                lantai_id: {required: true},
                ruangan_kode: {required: true },
                ruangan_nama: {required: true, minlength: 3, maxlength: 50},
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
                            dataRuangan.ajax.reload();
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