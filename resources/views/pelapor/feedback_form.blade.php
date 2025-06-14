<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <form id="formFeedback" action="{{ route('pelapor.feedback.store') }}" method="POST">
            @csrf
            <input type="hidden" name="tugas_id" value="{{ $tugas->tugas_id }}">

            <div class="modal-header">
                <h5 class="modal-title">Beri Ulasan Tugas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p><strong>Fasilitas:</strong> {{ $tugas->details->first()->fasilitas->fasilitas_nama ?? '-' }}</p>

                <div class="form-group">
                    <label>Rating</label>
                    <div>
                        @for($i = 1; $i <= 5; $i++)
                            <label class="mr-3">
                                <input type="radio" name="rating" value="{{ $i }}" required> {{ $i }}
                            </label>
                        @endfor
                    </div>
                </div>

                <div class="form-group">
                    <label>Ulasan</label>
                    <textarea name="ulasan" class="form-control" rows="3" placeholder="Tulis ulasan Anda..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Kirim Ulasan</button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });

        // DataTables
        $('#table_tugas').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pelapor.feedback.list') }}",
                dataType: "json",
                type: "GET",
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                { data: "tugas_jenis", defaultContent: "-" },
                { data: "details.0.fasilitas.fasilitas_nama", defaultContent: "-" },
                { data: "user.name", defaultContent: "-" },
                { data: "status.status_nama", defaultContent: "-", className: "text-center" },
                { data: "rating", className: "text-center", orderable: false, searchable: false },
                { data: "aksi", className: "text-center", orderable: false, searchable: false }
            ]
        });
    });

    // Fungsi untuk membuka modal dan bind validasi
    function modalAction(url = '') {
        $('#myModal').load(url, function(response, status, xhr) {
            if (status == "error") {
                var msg = "Maaf, terjadi kesalahan saat memuat detail: ";
                $(this).html('<div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-body"><div class="alert alert-danger">' + msg + xhr.status + " " + xhr.statusText + '</div></div></div></div>');
            }

            $(this).modal('show');

            // Jalankan validasi saat modal berhasil dimuat
            $('#formFeedback').validate({
                rules: {
                    rating: { required: true },
                    ulasan: { minlength: 10, maxlength: 255 }
                },
                submitHandler: function (form) {
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        dataType: "json",
                        success: function (response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                $('#table_tugas').DataTable().ajax.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message || 'Ulasan gagal disimpan.'
                                });
                            }
                        },
                        error: function (xhr) {
                            let msg = 'Terjadi kesalahan server.';
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                msg = Object.values(xhr.responseJSON.errors).join('\n');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: msg
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
    }
</script>
@endpush