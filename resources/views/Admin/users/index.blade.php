<!-- filepath: e:\Software\laragon\www\LaporSana\resources\views\admin\users\index.blade.php -->
@extends('layouts.admin.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar User</h3>
            <div class="card-tools">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah User
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
                            <select class="form-control" id="roles_id" name="roles_id">
                                <option value="">- Semua Role -</option>
                                @foreach($roles as $item)
                                    <option value="{{ $item->roles_id }}">{{ $item->roles_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Role</small>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered" id="table_users">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Avatar</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th>NIM/NIP</th>
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
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
            });
        }

        var dataUsers;
        $(document).ready(function () {
            dataUsers = $('#table_users').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    "url": "{{ route('admin.users.list') }}",
                    "dataType": "json",
                    "type": "GET",
                    "data": function (r) {
                        r.roles_id = $('#roles_id').val();
                    },
                    "error": function (xhr, error, thrown) {
                        console.log('Error pada DataTables:', error);
                        console.log('Status:', xhr.status);
                        console.log('Response:', xhr.responseText);
                        $('#table_users_processing').hide();
                        toastr.error('Gagal memuat data: ' + error);
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                    { 
                        data: 'avatar_img', 
                        className: 'text-center', 
                        orderable: false, 
                        searchable: false 
                    },
                    { data: 'username', className: '', orderable: true, searchable: true },
                    { data: 'name', className: '', orderable: true, searchable: true },
                    { data: 'role_nama', className: '', orderable: false, searchable: true },
                    { 
                        data: null, 
                        className: '', 
                        orderable: false, 
                        searchable: true,
                        render: function(data) {
                            if (data.NIM) return 'NIM: ' + data.NIM;
                            if (data.NIP) return 'NIP: ' + data.NIP;
                            return '-';
                        }
                    },
                    { 
                        data: 'aksi', 
                        className: 'text-center', 
                        orderable: false, 
                        searchable: false 
                    }
                ]
            });
            
            // Filter by role
            $('#roles_id').on('change', function () {
                dataUsers.ajax.reload();
            });
        });
    </script>
@endpush