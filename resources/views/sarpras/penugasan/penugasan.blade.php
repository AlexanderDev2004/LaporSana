@extends('layouts.sarpras.template')

@section('content')
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button type="button" onclick="modalAction('{{ route('sarpras.penugasan.create') }}')" class="btn btn-primary">Tambah Tugas</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="table_tugas">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Tugas</th>
                    <th>Fasilitas</th>
                    <th>Ruangan</th>
                    <th>Lantai</th>
                    <th>Teknisi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
@endsection

@push('js')
<script>
    function modalAction(url = ''){
        $('#myModal').load(url, function(response, status, xhr){
            if (status == "error") {
                var msg = "Maaf, terjadi kesalahan saat memuat detail: ";
                $(this).html('<div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-body"><div class="alert alert-danger">' + msg + xhr.status + " " + xhr.statusText + '</div></div></div></div>');
            }
            $(this).modal('show');
        });
    }

    $(document).ready(function(){
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });

        var table = $('#table_tugas').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('sarpras.penugasan.list') }}",
                dataType: "json",
                type: "GET",
            },
            columns: [
                { 
                    data: "DT_RowIndex", 
                    className: "text-center", 
                    orderable: false, 
                    searchable: false 
                },{ 
                    data: "tugas_jenis", 
                    defaultContent: "-" 
                },{ 
                    data: "details.0.fasilitas.fasilitas_nama", 
                    defaultContent: "-" 
                },{ 
                    data: "details.0.fasilitas.ruangan.ruangan_nama", 
                    defaultContent: "-" 
                },{ 
                    data: "details.0.fasilitas.ruangan.lantai.lantai_nama", 
                    defaultContent: "-" 
                },{ 
                    data: "user.name", 
                    defaultContent: "-" 
                },{ 
                    data: "status.status_nama", 
                    defaultContent: "-" 
                },{ 
                    data: "aksi", 
                    className: "text-center", 
                    orderable: false, 
                    searchable: false 
                }
            ]
        });

        // --- TAMBAHKAN BLOK SCRIPT INI ---
        $('#table_tugas').on('click', '.btn-hapus', function() {
            let deleteUrl = $(this).data('url');
            
            Swal.fire({
                title: 'Konfirmasi',
                text: "Anda yakin ingin menghapus tugas ini secara permanen?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE', // Menggunakan metode DELETE
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                table.ajax.reload(); // Muat ulang tabel
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Tidak dapat menghubungi server.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
