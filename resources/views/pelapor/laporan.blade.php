@extends('layouts.pelapor.template')

@section('content')
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button class="btn btn-primary" id="btn-tambah-laporan"><i class="fas fa-plus"></i> Tambah Laporan</button>
        </div>
    </div>
    <div class="card-body"> 
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table table-bordered table-striped table-hover table-sm" id="table_laporan"> 
            <thead> 
                <tr>
                    <th>No</th>
                    <th>Fasilitas</th>
                    <th>Ruangan</th>
                    <th>Lantai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr> 
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal untuk Tambah Laporan -->
<div class="modal fade" id="modal-tambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahLabel">Tambah Laporan Kerusakan Fasilitas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
            </div>
            <div class="modal-body" id="modal-tambah-body">
                <!-- Konten akan dimuat via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Detail Laporan -->
<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel">Detail Laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">x</span>
                </button>
            </div>
            <div class="modal-body" id="modal-detail-body">
                <!-- Konten akan dimuat via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    #table_laporan {
        border: 1px solid #ffffff;
    }
    #table_laporan th {
        border-color: #ffffff;
    }
    /* Pastikan modal di atas semua elemen */
    .modal {
        z-index: 1050;
    }
    .modal-backdrop {
        z-index: 1040;
    }
    /* Pastikan content-wrapper aman dari tumpang tindih */
    .content-wrapper {
        z-index: 1;
    }
    .main-sidebar {
        z-index: 1000;
    }
</style>
@endpush

@push('js') 
<script> 
    $(document).ready(function(){ 
        $('#table_laporan').DataTable({ 
            processing: true, 
            serverSide: true, 
            ajax: { 
                url: "{{ route('pelapor.list') }}", 
                dataType: "json", 
                type: "POST",
            }, 
            columns: [{ 
                data: "DT_RowIndex",  
                className: "text-center", 
                width: "5%", 
                orderable: false, 
                searchable: false 
            },{ 
                data: "details.0.fasilitas.fasilitas_nama",  
                className: "", 
                width: "15%", 
                orderable: true, 
                searchable: true,
                defaultContent: "-"
            },{ 
                data: "details.0.fasilitas.ruangan.ruangan_nama",  
                className: "", 
                width: "15%", 
                orderable: true, 
                searchable: true,
                defaultContent: "-"
            },{ 
                data: "details.0.fasilitas.ruangan.lantai.lantai_nama",  
                className: "", 
                width: "15%", 
                orderable: true, 
                searchable: true,
                defaultContent: "-"
            },{ 
                data: "status.status_nama",  
                className: "", 
                width: "15%", 
                orderable: true, 
                searchable: true,
                defaultContent: "-"
            },{ 
                data: "aksi",  
                className: "text-center", 
                width: "15%", 
                orderable: false, 
                searchable: false 
            }],
            responsive: true,
            columnDefs: [{
                targets: -1,
                data: null,
                defaultContent: '<button class="btn btn-info btn-sm btn-detail"><i class="fas fa-eye"></i> Detail</button>'
            }]
        }); 

        $('#table_laporan_filter input').unbind().bind('keyup', function(e){ 
            if(e.keyCode == 13){ 
                $('#table_laporan').DataTable().search(this.value).draw(); 
            } 
        }); 

        // Event untuk membuka modal tambah
        $('#btn-tambah-laporan').on('click', function() {
            $.ajax({
                url: "{{ route('pelapor.create') }}",
                type: 'GET',
                success: function(response) {
                    $('#modal-tambah-body').html(response);
                    $('#modal-tambah').modal('show');
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal memuat form tambah laporan!'
                    });
                }
            });
        });

        // Event untuk membuka modal detail
        $('#table_laporan').on('click', '.btn-detail', function() {
            var table = $('#table_laporan').DataTable();
            var data = table.row($(this).parents('tr')).data();
            var laporanId = data.laporan_id;

            $.ajax({
                url: "{{ route('pelapor.show', ':id') }}".replace(':id', laporanId),
                type: 'GET',
                success: function(response) {
                    $('#modal-detail-body').html(response);
                    $('#modal-detail').modal('show');
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal memuat detail laporan!'
                    });
                }
            });
        });

        // Event untuk menutup modal dan hapus backdrop
        $('#modal-tambah, #modal-detail').on('hidden.bs.modal', function() {
            $(this).find('.modal-body').html(''); // Kosongkan isi modal
            $('.modal-backdrop').remove(); // Hapus backdrop
            $('body').removeClass('modal-open'); // Pastikan body tidak terkunci
        });

        // Submit form tambah via AJAX dengan jQuery Validation
        $(document).on('submit', '#form-tambah', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            // Validasi form menggunakan jQuery Validation
            $('#form-tambah').validate({
                rules: {
                    lantai_id: { required: true },
                    ruangan_id: { required: true },
                    fasilitas_id: { required: true },
                    deskripsi: { required: true, maxlength: 255 },
                    foto_bukti: { extension: "jpeg|png|jpg|gif", filesize: 2048 }
                },
                messages: {
                    lantai_id: { required: "Lantai wajib dipilih" },
                    ruangan_id: { required: "Ruangan wajib dipilih" },
                    fasilitas_id: { required: "Fasilitas wajib dipilih" },
                    deskripsi: { 
                        required: "Deskripsi wajib diisi",
                        maxlength: "Deskripsi maksimal 255 karakter"
                    },
                    foto_bukti: { 
                        extension: "File harus berformat jpeg, png, jpg, atau gif",
                        filesize: "Ukuran file maksimal 2MB"
                    }
                },
                errorElement: 'small',
                errorClass: 'form-text text-danger',
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    $.ajax({
                        url: "{{ route('pelapor.store') }}",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            $('#modal-tambah').modal('hide');
                            $('#table_laporan').DataTable().ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: response.success || 'Laporan berhasil disimpan!'
                            });
                        },
                        error: function(xhr, status, error) {
                            $('#modal-tambah-body').html(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Gagal menyimpan laporan!'
                            });
                        }
                    });
                }
            });
        });

        // Custom validator untuk ukuran file
        $.validator.addMethod('filesize', function(value, element, param) {
            return this.optional(element) || (element.files[0].size <= param * 1024);
        }, 'Ukuran file maksimal {0} KB');
    }); 
</script> 
@endpush