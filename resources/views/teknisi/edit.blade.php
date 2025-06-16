@empty($tugas)
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
                <a href="{{ url('/tugas') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    @else
        <form action="{{ route('teknisi.update', $tugas->tugas_id) }}" method="POST" id="form-edit">
            @csrf
            @method('PUT')
            <div id="modal-master" class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Data Perbaikan Tugas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Teknisi</label>
                            <input value="{{ $tugas->user->name }}" type="text" name="name" id="name"
                                class="form-control" readonly>
                            <small id="error-name" class="error-text form-text text-danger"></small>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status_nama" id="status_nama" class="form-control" required>
                                <option value="diproses" {{ $tugas->status->status_nama == 'diproses' ? 'selected' : '' }}>
                                    Diproses</option>
                                <option value="selesai" {{ $tugas->status->status_nama == 'selesai' ? 'selected' : '' }}>
                                    Selesai
                                </option>
                            </select>
                            <small id="error-status_nama" class="error-text form-text text-danger"></small>
                        </div>


                        <div class="form-group">
                            <label>Jenis Tugas</label>
                            <input type="text" class="form-control" value="{{ $tugas->tugas_jenis }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Penugasan</label>
                            <input value="{{ \Carbon\Carbon::parse($tugas->tugas_mulai)->format('d-m-Y') }}" type="date"
                                class="form-control" readonly disabled>
                            <small id="error-tugas_mulai" class="error-text form-text text-danger"></small>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                             <input value="{{ \Carbon\Carbon::parse($tugas->tugas_selesai)->format('Y-m-d') }}" type="date" name="tugas_selesai" id="tugas_selesai" class="form-control">
                            <small id="error-tugas_selesai" class="error-text form-text text-danger"></small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>

        <script>
            $(document).ready(function() {
                $('#form-edit').validate({
                    rules: {
                        status_nama: {
                            required: true
                        },
                        tugas_jenis: {
                            required: true
                        },
                        tugas_selesai: {
                            required: false,
                            date: true
                        }
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
                                    tableTugas.ajax.reload();
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
