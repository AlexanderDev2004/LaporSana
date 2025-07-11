@extends('layouts.sarpras.template')

@section('content')
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button type="button" onclick="modalAction('{{ route('sarpras.pemeriksaan.create') }}')" class="btn btn-primary">Tambah Pemeriksaan</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="table_tugas">
            <thead>
                <tr>
                    <th>No</th>
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
    function modalAction(url = '') {
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
                url: "{{ route('sarpras.pemeriksaan.list') }}",
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

        $('#table_tugas').on('click', '.btn-hapus', function() {
        let deleteUrl = $(this).data('url');
        Swal.fire({
            title: 'Konfirmasi',
            text: "Yakin ingin menghapus tugas ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#ffc107'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            $('#table_tugas').DataTable().ajax.reload();
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal menghubungi server.', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
