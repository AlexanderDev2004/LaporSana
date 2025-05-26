@extends('layouts.admin.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah User Baru</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role_id">Role</label>
                    <select class="form-control" id="roles_id" name="roles_id" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->roles_id }}">{{ $role->roles_nama }}</option>
                        @endforeach
                    </select>

                </div>
                <div class="form-group">
                    <label for="NIM">NIM (untuk Mahasiswa)</label>
                    <input type="text" class="form-control" id="NIM" name="NIM">
                </div>
                <div class="form-group">
                    <label for="NIP">NIP (untuk Dosen/Tendik)</label>
                    <input type="text" class="form-control" id="NIP" name="NIP">
                </div>
                <div class="form-group">
                    <label for="avatar">Avatar</label>
                    <input type="file" class="form-control-file" id="avatar" name="avatar">
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-default">Batal</a>
            </form>
        </div>
    </div>
@endsection
