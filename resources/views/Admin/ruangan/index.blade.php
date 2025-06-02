@extends('layouts.admin.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Ruangan</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ route('admin.ruangan.create') }}')" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Ruangan
                </button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group-row">
                        <label class="col-1 control-label col-form-label">Filter:</label>
                        <div class="col-3">
                            <select class="form-control" id="lantai_id" name="lantai_id" required>
                                <option value="">- Semua -</option>
                                @foreach($lantai as $item)
                                    <option value="{{ $item->lantai_id }}">{{ $item->lantai_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Lantai</small>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered" id="table_ruangan">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Ruangan</th>
                        <th>Nama Ruangan</th>
                        <th>Lantai</th>
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
        
        var dataRuangan;
        $(document).ready(function(){
            dataRuangan = $('#table_ruangan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('admin.ruangan.list') }}",
                    "dataType": "json",
                    "type": "GET",
                    "data": function (r) {
                        r.lantai_id = $('#lantai_id').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
                    {data: 'ruangan_kode', className: '', orderable: true, searchable: true},
                    {data: 'ruangan_nama', className: '', orderable: true, searchable: true},
                    {data: 'lantai.lantai_nama', className: '', orderable: false, searchable: false},
                    {data: 'aksi', className: '', orderable: false, searchable: false}
                ]
            });
             $('#lantai_id').on('change', function() {
                dataRuangan.ajax.reload();
            });
        });
    </script>
@endpush