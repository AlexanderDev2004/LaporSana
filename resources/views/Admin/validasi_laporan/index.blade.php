@extends('layouts.admin.template')

@section('title', 'Validasi Laporan')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Validasi Laporan</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Laporan Masuk</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="laporanDataTable" width="100%" cellspacing="0">
                        <table id="laporanTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID Laporan</th>
                                    <th>Pelapor</th>
                                    <th>Tanggal Lapor</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>

                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Laporan -->
    <div class="modal fade" id="detailLaporanModal" tabindex="-1" role="dialog" aria-labelledby="detailLaporanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Laporan</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <p>Memuat...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function () {
    var laporanTable = $('#laporanTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.validasi_laporan.list") }}',
        columns: [
            { data: 'laporan_id', name: 'laporan_id' },
            { data: 'pelapor', name: 'pelapor' },
            { data: 'tanggal', name: 'tanggal' },
            { data: 'status', name: 'status' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
        ]
    });

    window.modalAction = function(url) {
        $('#modalContent').html('<p>Memuat...</p>');
        $.get(url, function(res) {
            $('#modalContent').html(res);
            $('#detailLaporanModal').modal('show');
        }).fail(function() {
            $('#modalContent').html('<p>Gagal memuat detail laporan.</p>');
        });
    };

    window.setujuAction = function(id) {
        if (confirm('Setujui laporan ini?')) {
            $.post("{{ url('admin/validasi_laporan') }}/" + id + "/setuju", {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                alert(res.message);
                laporanTable.ajax.reload();
            });
        }
    };

    window.tolakAction = function(id) {
        if (confirm('Tolak laporan ini?')) {
            $.post("{{ url('admin/validasi_laporan') }}/" + id + "/tolak", {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                alert(res.message);
                laporanTable.ajax.reload();
            });
        }
    };
});
</script>
@endpush
