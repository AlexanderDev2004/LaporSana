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

            {{-- Filter Role dan Search --}}
            <form method="GET" action="{{ route('admin.users.index') }}" class="form-inline mb-3 row">
                <div class="form-group col-md-3">
                    <label for="role" class="mr-2">Filter Role:</label>
                    <select name="role" id="role" class="form-control mr-2 w-100">
                        <option value="">- Semua -</option>
                        @foreach ($roles as $item)
                            <option value="{{ $item->roles_id }}" {{ request('role') == $item->roles_id ? 'selected' : '' }}>
                                {{ $item->roles_nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="form-group col-md-3">
                    <label for="search" class="mr-2">Search:</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control w-100" placeholder="Cari username/nama...">
                </div> --}}
                <div class="form-group col-md-2">
                    <button type="submit" class="btn btn-primary btn-block mt-4">Terapkan</button>
                </div>
            </form>

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
            <div class="mt-3">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        {{-- Previous Page Link --}}
                        @if ($users->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">&laquo; Previous</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $users->previousPageUrl() }}" rel="prev">&laquo; Previous</a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            <li class="page-item {{ $page == $users->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($users->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $users->nextPageUrl() }}" rel="next">Next &raquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">Next &raquo;</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        /* Tambahan untuk kecilkan pagination DataTables jika tetap muncul */
        .dataTables_paginate .paginate_button {
            font-size: 0.875rem !important;
            padding: 0.25rem 0.5rem !important;
        }
    </style>
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#user-table').DataTable({
                responsive: true,
                searching: false,   // Nonaktifkan search bawaan
                paging: false,      // Nonaktifkan paging bawaan
                info: false         // Nonaktifkan info bawah
            });
        });
    </script>
@endpush
