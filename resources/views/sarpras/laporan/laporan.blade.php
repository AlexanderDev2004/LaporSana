@extends('layouts.sarpras.template')

@section('content')
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="table_laporan">
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

        var table = $('#table_laporan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('sarpras.laporan.list') }}",
                dataType: "json",
                type: "POST",
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
                    data: "status.status_nama", 
                    defaultContent: "-" 
                },{ 
                    data: "user.name", 
                    defaultContent: "-" 
                },{ 
                    data: "jumlah_pelapor", 
                    defaultContent: "-" 
                },{ 
                    data: "aksi", 
                    className: "text-center", 
                    orderable: false, 
                    searchable: false 
                }
            ]
        });
        // --- SCRIPT BARU UNTUK UPDATE STATUS ---
        $('#table_laporan').on('click', '.btn-update-status', function() {
            let laporanId = $(this).data('id');
            let newStatusId = $(this).data('status');
            let statusText = newStatusId == 4 ? 'Selesai' : 'Ditolak';
            let url = '{{ url("sarpras/laporan") }}/' + laporanId + '/update-status';

            Swal.fire({
                title: 'Konfirmasi',
                text: "Anda yakin ingin mengubah status laporan ini menjadi '" + statusText + "'?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: newStatusId == 4 ? '#28a745' : '#d33',
                cancelButtonColor: '#ffc107',
                confirmButtonText: 'Ya, Ubah Status!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(url, { status_id: newStatusId })
                        .done(function(response) {
                            if (response.status) {
                                Swal.fire('Berhasil!', response.message, 'success');
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        })
                        .fail(function() {
                            Swal.fire('Error!', 'Tidak dapat menghubungi server.', 'error');
                        });
                }
            });
        });
    });
</script>
@endpush