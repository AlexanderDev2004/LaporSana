@extends('layouts.admin.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit User</h3>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror"
                       id="username" name="username" value="{{ old('username', $user->username) }}" required>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="NIM">NIM (Opsional)</label>
                <input type="text" class="form-control @error('NIM') is-invalid @enderror"
                       id="NIM" name="NIM" value="{{ old('NIM', $user->NIM) }}">
                @error('NIM')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="NIP">NIP (Opsional)</label>
                <input type="text" class="form-control @error('NIP') is-invalid @enderror"
                       id="NIP" name="NIP" value="{{ old('NIP', $user->NIP) }}">
                @error('NIP')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       id="password" name="password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="roles_id">Role</label>
                <select class="form-control @error('roles_id') is-invalid @enderror"
                        id="roles_id" name="roles_id" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->roles_id }}"
                            {{ old('roles_id', $user->roles_id) == $role->roles_id ? 'selected' : '' }}>
                            {{ $role->roles_nama }}
                        </option>
                    @endforeach
                </select>
                @error('roles_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="avatar">Avatar</label>
                <input type="file" class="form-control-file @error('avatar') is-invalid @enderror"
                       id="avatar" name="avatar">
                @error('avatar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @if($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" width="100" class="mt-2">
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-default">Batal</a>
        </form>
    </div>
</div>
@endsection
