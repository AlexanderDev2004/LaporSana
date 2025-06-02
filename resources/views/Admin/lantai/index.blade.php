@extends('layouts.admin.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Lantai</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ route('admin.lantai.create') }}')" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Lantai
                </button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered" id="table_lantai">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Lantai</th>
                        <th>Nama Lantai</th>
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
        
        var dataLantai;
        $(document).ready(function(){
            dataLantai = $('#table_lantai').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('admin.lantai.list') }}",
                    "dataType": "json",
                    "type": "GET",
                },
                columns: [
                    {data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
                    {data: 'lantai_kode', className: '', orderable: true, searchable: true},
                    {data: 'lantai_nama', className: '', orderable: true, searchable: true},
                    {data: 'aksi', className: '', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endpush