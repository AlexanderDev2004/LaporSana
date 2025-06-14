@extends('layouts.pelapor.template')

@section('content')
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
    </div>

    <div class="card-body">
        <div id="feedback-alert" class="alert d-none" role="alert"></div>
        <table class="table table-bordered table-striped table-hover table-sm" id="table_tugas">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Tugas</th>
                    <th>Fasilitas</th>
                    <th>Teknisi</th>
                    <th>Status</th>
                    <th>Rating</th> {{-- Tambahan --}}
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
{{-- Pastikan FontAwesome tersedia --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
                { data: "rating", className: "text-center", orderable: false, searchable: false }, // Kolom rating
                { data: "aksi", className: "text-center", orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush
