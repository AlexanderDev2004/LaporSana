@extends('layouts.admin.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar User</h3>
            <div class="card-tools">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah User
                </a>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Filter Role --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="form-inline mb-3">
                <label for="role" class="mr-2">Filter Role:</label>
                <select name="role" id="role" class="form-control mr-2">
                    <option value="">- Semua -</option>
                    @foreach ($roles as $item)
                        <option value="{{ $item->roles_id }}" {{ request('role') == $item->roles_id ? 'selected' : '' }}>
                            {{ $item->roles_nama }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Terapkan</button>
            </form>
            <div class="col-md-3 offset-md-6 text-right">
                    <label for="search" class="control-label">Search:</label>
                    <input type="text" id="search" class="form-control" placeholder="Search...">
                </div>
            {{-- Tabel User --}}
            <table id="user-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Avatar</th>
                        <th>Role</th>
                        <th>NIM</th>
                        <th>NIP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->user_id }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->name ?? $user->nama }}</td>
                            <td>
                                @if ($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" width="50" class="img-circle">
                                @else
                                    <img src="{{ asset('LaporSana/dist/img/user2-160x160.jpg') }}" width="50" class="img-circle">
                                @endif
                            </td>
                            <td>{{ $user->role->roles_nama ?? '-' }}</td>
                            <td>{{ $user->NIM ?? '-' }}</td>
                            <td>{{ $user->NIP ?? '-' }}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info mr-1" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning mr-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#user-table').DataTable({
                responsive: true,
                language: {
                    lengthMenu: "Tampilkan _MENU_ entri",
                    zeroRecords: "Tidak ditemukan data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                    infoFiltered: "(difilter dari _MAX_ total entri)",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                },
                pageLength: 10
            });
        });
    </script>
@endpush
