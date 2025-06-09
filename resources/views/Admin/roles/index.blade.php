@extends('layouts.admin.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Role</h3>
            <div class="card-tools">
                 <button onclick="modalAction(`{{ route('admin.roles.import') }}`)" class="btn btn-info">Import Role</button>
                <a href="{{ route('admin.roles.export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export
                    Role</a>
                    <a href="{{ route('admin.roles.export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export
                        Role</a>
                <button onclick="modalAction('{{ route('admin.roles.create') }}')" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah role
                </button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered" id="table_role">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode role</th>
                        <th>Nama role</th>
                        <th>Poin role</th>
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
        
        var dataRoles;
        $(document).ready(function(){
            dataRoles = $('#table_role').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('admin.roles.list') }}",
                    "dataType": "json",
                    "type": "GET",
                },
                columns: [
                    {data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
                    {data: 'roles_kode', className: '', orderable: true, searchable: true},
                    {data: 'roles_nama', className: '', orderable: true, searchable: true},
                    {data: 'poin_roles', className: '', orderable: true, searchable: true},
                    {data: 'aksi', className: '', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endpush