@extends('layouts.admin.template')

@section('title', $breadcrumb->title)

@section('content')
<div class="container">
    <h4>{{ $breadcrumb->title }}</h4>

    <table id="laporanTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pelapor</th>
                <th>Fasilitas</th>
                <th>Status</th>
                <th>Tanggal Lapor</th>
                <th>Aksi</th>
            </tr>
        </thead>
    </table>
</div>

{{-- Modal for showing report details --}}
<div class="modal fade" id="laporanModal" tabindex="-1" aria-labelledby="laporanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="modalContent">
            {{-- Content will be loaded here via AJAX --}}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function () {
        $('#laporanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("admin.laporan.list") }}',
            language: {
                processing: '<i class="fa fa-spinner fa-spin"></i> Memuat...'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'nama_user', name: 'nama_user' },
                { data: 'nama_fasilitas', name: 'nama_fasilitas' },
                { data: 'status', name: 'status' },
                { data: 'tanggal_lapor', name: 'tanggal_lapor' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
            ]
        });
    });

    function modalAction(url) {
        $.get(url, function (res) {
            $('#modalContent').html(res);
            $('#laporanModal').modal('show');
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat memuat detail laporan.',
            });
        });
    }

    function verifyLaporan(laporanId, action) {
        Swal.fire({
            title: `Apakah Anda yakin ingin ${action == 'setujui' ? 'menyetujui' : 'menolak'} laporan ini?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("admin/laporan/verify") }}/' + laporanId,
                    method: 'POST',
                    data: {
                        verifikasi: action,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                            });
                            $('#laporanTable').DataTable().ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat memproses laporan.',
                        });
                    }
                });
            }
        });
    }
</script>
@endsection
