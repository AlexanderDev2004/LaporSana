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
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Form Filter -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="form-inline mb-3">
                <label for="role" class="mr-2">Filter Role:</label>
                <select name="role" id="role" class="form-control mr-2">
                    <option value="">- Semua Role -</option>
                    @foreach ($roles as $item)
                        <option value="{{ $item->roles_id }}"
                            {{ request('role') == $item->roles_id ? 'selected' : '' }}>
                            {{ $item->roles_nama }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            <!-- Tabel User -->
            <table class="table table-bordered table-hover">
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
                            <td>{{ $user->nama }}</td>
                            <td>
                                @if ($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" width="50" class="img-circle" alt="Avatar">
                                @else
                                    <img src="{{ asset('LaporSana/dist/img/user2-160x160.jpg') }}" width="50" class="img-circle" alt="Default Avatar">
                                @endif
                            </td>
                            <td>{{ $user->role->roles_nama ?? 'Tidak Ada Role' }}</td>
                            <td>{{ $user->NIM ?? '-' }}</td>
                            <td>{{ $user->NIP ?? '-' }}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                        class="btn btn-sm btn-info btn-actions mr-1" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="btn btn-sm btn-warning btn-actions mr-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger btn-actions" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data user ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
