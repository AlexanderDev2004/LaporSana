@extends('layouts.sarpras.template')

@section('content')
<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="table_laporan">
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true"></div>
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

$(document).ready(function() {
    $('#table_laporan').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('sarpras.riwayat.penugasan.list') }}",
        columns: [
            { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
            { data: "tugas_jenis", defaultContent: "-" },
            { data: "details.0.fasilitas.fasilitas_nama", defaultContent: "-" },
            { data: "details.0.fasilitas.ruangan.ruangan_nama", defaultContent: "-" },
            { data: "details.0.fasilitas.ruangan.lantai.lantai_nama", defaultContent: "-" },
            { data: "user.name", defaultContent: "-" },
            { data: "status.status_nama", defaultContent: "-" },
            { data: "aksi", orderable: false, searchable: false, className: "text-center" }
        ]
    });
});
</script>
@endpush
