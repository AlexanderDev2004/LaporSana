@empty($tugas)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
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
    </div>
@else
    <form action="{{ route('teknisi.update', $tugas->tugas_id) }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')

        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Pemeriksaan</h5>
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
                        <input type="text" class="form-control" value="{{ ($tugas->status->status_nama) }}"
                            readonly>
                    </div>


                    <div class="form-group">
                        <label>Jenis Tugas</label>
                        <input type="text" class="form-control" value="{{ $tugas->tugas_jenis }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Penugasan</label>
                        <input value="{{ \Carbon\Carbon::parse($tugas->tugas_mulai)->format('Y-m-d') }}" type="date"
                            class="form-control" readonly disabled>
                        <small id="error-tugas_mulai" class="error-text form-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label>Tingkat Kerusakan</label>
                        <select name="tingkat_kerusakan" id="tingkat_kerusakan" class="form-control" required>
                            <option value="">-- Pilih Tingkat Kerusakan --</option>
                            <option value="1" {{ $tugas->tingkat_kerusakan == 1 ? 'selected' : '' }}>1 - Tidak Parah
                            </option>
                            <option value="2" {{ $tugas->tingkat_kerusakan == 2 ? 'selected' : '' }}>2 - Sedikit Parah
                            </option>
                            <option value="3" {{ $tugas->tingkat_kerusakan == 3 ? 'selected' : '' }}>3 - Cukup Parah
                            </option>
                            <option value="4" {{ $tugas->tingkat_kerusakan == 4 ? 'selected' : '' }}>4 - Parah
                            </option>
                            <option value="5" {{ $tugas->tingkat_kerusakan == 5 ? 'selected' : '' }}>5 - Sangat Parah
                            </option>
                        </select>
                        <small id="error-tingkat_kerusakan" class="error-text form-text text-danger"></small>
                    </div>


                    <div class="form-group">
                        <label>Foto Bukti</label>
                        <input type="file" name="tugas_image" id="tugas_image" class="form-control" accept="image/*">
                        @if ($tugas->tugas_image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $tugas->tugas_image) }}" alt="Foto Bukti"
                                    style="max-width:120px;">
                            </div>
                        @endif
                        <small id="error-tugas_image" class="error-text form-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label>Biaya Perbaikan</label>
                        <input value="{{ $tugas->biaya_perbaikan }}" type="text" name="biaya_perbaikan"
                            id="biaya_perbaikan" class="form-control" onkeyup="formatRupiah(this)">
                        <small id="error-biaya_perbaikan" class="error-text form-text text-danger"></small>
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
        <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
            data-keyboard="false" data-width="75%" aria-hidden="true"></div>
    </form>

    <script>
        $(document).ready(function() {
            $('#form-edit').validate({
                rules: {
                    tingkat_kerusakan: {
                        required: true,
                    },
                    foto_bukti: {
                        required: false,
                    },
                    foto_bukti: {
                        required: false,
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
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });

        function formatRupiah(input) {
            let value = input.value.replace(/[^,\d]/g, '').toString();
            let split = value.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            input.value = rupiah ? 'Rp ' + rupiah : '';
        }
    </script>
@endempty
