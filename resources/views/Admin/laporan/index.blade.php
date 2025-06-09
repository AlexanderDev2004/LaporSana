@extends('layouts.admin.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Laporan</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ route('admin.laporan.create') }}')" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Lantai
                </button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered" id="table_laporan">
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
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        var dataLaporan;
        $(document).ready(function(){
            // Add status filter dropdown above the table
            $('#table_laporan').before(`
                <div class="form-group mb-3">
                    <label for="status_filter">Filter Status:</label>
                    <select id="status_filter" class="form-control" style="width: 200px;">
                        <option value="">Semua Status</option>
                        <option value="1">Menunggu Verifikasi</option>
                        <option value="2">Ditolak</option>
                        <option value="3">Diproses</option>
                        <option value="4">Selesai</option>
                    </select>
                </div>
            `);
            
            dataLaporan = $('#table_laporan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('admin.laporan.list') }}",
                    "dataType": "json",
                    "type": "GET",
                    "data": function(d) {
                        d.status_id = $('#status_filter').val();
                        return d;
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
                    {data: 'user.username', className: '', orderable: true, searchable: true},
                    {
                        data: 'status.status_nama', 
                        className: '', 
                        orderable: true, 
                        searchable: true,
                        render: function(data, type, row) {
                            let badgeClass = 'badge badge-secondary';
                            
                            // Assign badge colors based on status_id
                            if (row.status_id == 1) badgeClass = 'badge badge-warning';
                            else if (row.status_id == 2) badgeClass = 'badge badge-danger';
                            else if (row.status_id == 3) badgeClass = 'badge badge-primary';
                            else if (row.status_id == 4) badgeClass = 'badge badge-success';
                            
                            return '<span class="' + badgeClass + '">' + data + '</span>';
                        }
                    },
                    {data: 'tanggal_lapor', className: '', orderable: true, searchable: true},
                    {data: 'jumlah_pelapor', className: '', orderable: true, searchable: true},
                    {data: 'aksi', className: '', orderable: false, searchable: false}
                ]
            });
            
            // Reload table when status filter changes
            $('#status_filter').on('change', function(){
                dataLaporan.ajax.reload();
            });
        });
    </script>
@endpush