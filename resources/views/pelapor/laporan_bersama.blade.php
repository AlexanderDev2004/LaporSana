@extends('layouts.pelapor.template')

@section('content')
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="table_laporan_bersama">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Fasilitas</th>
                    <th>Ruangan</th>
                    <th>Lantai</th>
                    <th>Status</th>
                    <th>Pelapor</th>
                    <th>Jumlah Pelapor</th>
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

        var table = $('#table_laporan_bersama').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pelapor.list.bersama') }}",
                dataType: "json",
                type: "POST",
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                { data: "details.0.fasilitas.fasilitas_nama", defaultContent: "-" },
                { data: "details.0.fasilitas.ruangan.ruangan_nama", defaultContent: "-" },
                { data: "details.0.fasilitas.ruangan.lantai.lantai_nama", defaultContent: "-" },
                { data: "status.status_nama", defaultContent: "-" },
                { data: "user.name", defaultContent: "-" },
                { data: "jumlah_pelapor", defaultContent: "-" },
                { data: "aksi", className: "text-center", orderable: false, searchable: false }
            ]
        });

        // Event listener untuk tombol "Ikut Melapor"
        $('#table_laporan_bersama').on('click', '.btn-dukung', function() {
            // Perbaikan: Ambil URL dari atribut 'data-url'
            let url = $(this).data('url');

            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin ikut melaporkan kerusakan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#ffc107',
                confirmButtonText: 'Ya, Ikut Melapor!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(url)
                        .done(function(response) {
                            if (response.status) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                table.ajax.reload(null, false); // Reload tabel
                            } else {
                                Swal.fire('Info', response.message, 'info');
                            }
                        })
                        .fail(function(jqXHR) {
                            let errorMsg = jqXHR.responseJSON ? jqXHR.responseJSON.message : 'Tidak dapat menghubungi server.';
                            Swal.fire('Gagal!', errorMsg, 'error');
                        });
                }
            });
        });
    });
</script>
@endpush