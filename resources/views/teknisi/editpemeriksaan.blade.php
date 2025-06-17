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
                <a href="{{ url('/teknisi/pemeriksaan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ route('teknisi.updatepemeriksaan', $tugas->tugas_id) }}" method="POST" id="form-edit" enctype="multipart/form-data">
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
                        <input type="text" class="form-control" value="{{ $tugas->status->status_nama }}" readonly>
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
                            <option value="1" {{ $tugas->tingkat_kerusakan == 1 ? 'selected' : '' }}>1 - Tidak sangat parah</option>
                            <option value="2" {{ $tugas->tingkat_kerusakan == 2 ? 'selected' : '' }}>2 - Tidak cukup parah</option>
                            <option value="3" {{ $tugas->tingkat_kerusakan == 3 ? 'selected' : '' }}>3 - Parah</option>
                            <option value="4" {{ $tugas->tingkat_kerusakan == 4 ? 'selected' : '' }}>4 - Cukup Parah</option>
                            <option value="5" {{ $tugas->tingkat_kerusakan == 5 ? 'selected' : '' }}>5 - Sangat Parah</option>
                        </select>
                        <small id="error-tingkat_kerusakan" class="error-text form-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label>Foto Bukti</label>
                        <input type="file" name="tugas_image" id="tugas_image" class="form-control" accept="image/*">
                        @if ($tugas->tugas_image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $tugas->tugas_image) }}" alt="Foto Bukti" style="max-width:120px;">
                                <p class="text-muted mt-1">Foto saat ini</p>
                            </div>
                        @endif
                        <small id="error-tugas_image" class="error-text form-text text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label>Biaya Perbaikan</label>
                        <input value="{{ $tugas->biaya_perbaikan ? number_format($tugas->biaya_perbaikan, 2, '.', '') : '' }}" 
                               type="number" 
                               name="biaya_perbaikan" 
                               id="biaya_perbaikan" 
                               class="form-control" 
                               min="0" 
                               step="0.01">
                        <small id="error-biaya_perbaikan" class="error-text form-text text-danger"></small>
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
        $('#form-edit').validate({
            rules: {
                tingkat_kerusakan: {
                    required: true,
                },
                biaya_perbaikan: {
                    number: true,
                    min: 0
                },
                tugas_image: {
                    accept: "image/*"
                }
            },
            messages: {
                tingkat_kerusakan: {
                    required: "Tingkat kerusakan harus dipilih"
                },
                biaya_perbaikan: {
                    number: "Biaya harus berupa angka",
                    min: "Biaya tidak boleh negatif"
                },
                tugas_image: {
                    accept: "Hanya file gambar yang diperbolehkan"
                }
            },
            submitHandler: function(form) {
                var formData = new FormData(form);
                
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#modal-master').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                location.reload(); // Reload halaman untuk melihat perubahan
                            });
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
                    },
                    error: function(xhr) {
                        var errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengirim data';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
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
    </script>
@endempty