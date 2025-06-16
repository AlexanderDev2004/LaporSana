@extends('layouts.admin.template')

@section('content')
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <div class="form-inline">
                    <label class="mr-2" for="status_filter">Filter Status:</label>
                    <select id="status_filter" class="form-control form-control-sm">
                        <option value="">Semua Status</option>
                        <option value="1">Menunggu Verifikasi</option>
                        <option value="2">Ditolak</option>
                        <option value="3">Diproses</option>
                        <option value="5">Disetujui</option>
                    </select>
                </div>
                <div class="ml-auto">
                <a href="{{ route('admin.validasi_laporan.export_excel') }}" class="btn btn-primary mr-2">
                    <i class="fa fa-file-excel"></i> Export Laporan
                </a>
                <a href="{{ route('admin.validasi_laporan.export_pdf') }}" class="btn btn-warning">
                    <i class="fa fa-file-pdf"></i> Export Laporan
                </a>
            </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="laporanTable" class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pelapor</th>
                                <th>Status</th>
                                <th>Tanggal Lapor</th>
                                <th>Jumlah Pelapor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk menampilkan detail laporan -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
        <!-- Konten modal akan dimuat secara dinamis -->
    </div>
@endsection

@push('scripts')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
            });
        }

        var dataLaporan; // Mengubah nama variabel agar konsisten dengan show.blade.php
        $(document).ready(function () {
            dataLaporan = $('#laporanTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,

                ajax: {
                    "url": "{{ route('admin.validasi_laporan.list') }}", // Mengubah URL ke route yang benar
                    "dataType": "json",
                    "type": "GET",
                    "data": function (d) {
                        d.status_id = $('#status_filter').val();
                    },
                    "error": function (xhr, error, thrown) {
                        console.log('Error pada DataTables:', error);
                        console.log('Status:', xhr.status);
                        console.log('Response:', xhr.responseText);
                        $('#laporanTable_processing').hide();
                        toastr.error('Gagal memuat data: ' + error);
                    }
                },
                columns: [
                    { data: 'laporan_id', className: 'text-center' },
                    { data: 'user.name', className: '', orderable: true, searchable: true },
                    {
                        data: 'status.status_nama',
                        className: 'text-center',
                        render: function(data, type, row) {
                            let badgeClass = 'badge badge-secondary';

                            if (row.status_id == 1)
                                badgeClass = 'badge badge-warning';
                            else if (row.status_id == 2)
                                badgeClass = 'badge badge-danger';
                            else if (row.status_id == 3)
                                badgeClass = 'badge badge-primary';
                            else if (row.status_id == 4)
                                badgeClass = 'badge badge-success';
                            else if (row.status_id == 5)
                                badgeClass = 'badge badge-info';

                            return '<span class="' + badgeClass + '">' + data + '</span>';
                        }
                    },
                    { data: 'tanggal_lapor', className: '', orderable: true, searchable: true },
                    { data: 'jumlah_pelapor', className: 'text-center', orderable: true, searchable: true },
                    { data: 'aksi', className: 'text-center', orderable: false, searchable: false }
                ]
            });

            // Filter berdasarkan status
            $('#status_filter').on('change', function () {
                dataLaporan.ajax.reload();
            });
        });
    </script>
@endpush
