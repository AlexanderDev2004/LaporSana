@empty($user)
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
                <a href="{{ route('admin.users.index') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ route('admin.users.delete', $user->user_id) }}" method="POST" id="form-delete">
        @csrf
        @method('DELETE')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Data User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-ban"></i> Konfirmasi !!!</h5>
                        Apakah Anda ingin menghapus data user seperti di bawah ini?
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 text-center">
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" class="img-circle elevation-2" width="100" 
                                    height="100" alt="User Avatar">
                            @else
                                <img src="{{ asset('LaporSana/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" 
                                    width="100" height="100" alt="Default Avatar">
                            @endif
                        </div>
                    </div>
                    <table class="table table-sm table-bordered table-striped">
                        <tr>
                            <th class="text-right col-3">ID User :</th>
                            <td class="col-9">{{ $user->user_id }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Username :</th>
                            <td class="col-9">{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Nama Lengkap :</th>
                            <td class="col-9">{{ $user->name }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">Role :</th>
                            <td class="col-9">{{ $user->role->roles_nama }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">NIM :</th>
                            <td class="col-9">{{ $user->NIM ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-right col-3">NIP :</th>
                            <td class="col-9">{{ $user->NIP ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </form>
    <script>
        $(document).ready(function() {
            $("#form-delete").validate({
                rules: {},
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
                                dataUsers.ajax.reload();
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
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
@endempty