@extends('layouts.admin.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail User</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    @if ($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="img-circle elevation-2" width="150"
                            alt="User Avatar">
                    @else
                        <img src="{{ asset('LaporSana/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                            width="150" alt="Default Avatar">
                    @endif
                    <h4 class="mt-3">{{ $user->nama }}</h4>
                    <p class="text-muted">{{ $user->role->roles_nama }}</p>
                </div>
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Username</th>
                            <td>{{ $user->username }}</td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap</th>
                            <td>{{ $user->nama }}</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>{{ $user->role->roles_nama }}</td>
                        </tr>
                        <tr>
                            <th>NIM</th>
                            <td>{{ $user->NIM ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>NIP</th>
                            <td>{{ $user->NIP ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Dibuat</th>
                            <td>{{ $user->created_at ? $user->created_at->format('d F Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diupdate</th>
                            <td>{{ $user->updated_at ? $user->updated_at->format('d F Y H:i') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.users.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary float-right">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>
@endsection
