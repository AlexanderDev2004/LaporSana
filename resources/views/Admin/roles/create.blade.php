<form action="{{ route('admin.roles.store') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Kode roles</label>
                    <input type="text" name="roles_kode" id="roles_kode" class="form-control" required>
                    <small id="error-roles_kode" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label>Nama roles</label>
                    <input type="text" name="roles_nama" id="roles_nama" class="form-control" required>
                    <small id="error-roles_nama" class="error-text form-text text-danger"></small>
                </div>
                 <div class="form-group">
                    <label>Poin roles</label>
                    <input type="text" name="poin_roles" id="poin_roles" class="form-control" required>
                    <small id="error-poin_roles" class="error-text form-text text-danger"></small>
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
                roles_kode: {required: true, maxlength: 5 },
                roles_nama: {required: true, minlength: 3, maxlength: 50},
                poin_roles: {required: true, number: true, min: 0}
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
                            dataRoles.ajax.reload();
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
