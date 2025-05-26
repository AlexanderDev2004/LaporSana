@extends('layouts.admin.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit User</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
            </div>
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="{{ $user->nama }}" required>
            </div>
            <div class="form-group">
                <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="role_id">Role</label>
                <select class="form-control" id="role_id" name="role_id" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->roles_id }}" {{ $user->roles_id == $role->roles_id ? 'selected' : '' }}>
                            {{ $role->roles_nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="avatar">Avatar</label>
                <input type="file" class="form-control-file" id="avatar" name="avatar">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" width="100" class="mt-2">
                @endif
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-default">Batal</a>
        </form>
    </div>
</div>
@endsection
