@extends('layouts.admin.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Role</h3>
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

        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="roles_nama">Nama Role</label>
                <input type="text" class="form-control @error('roles_nama') is-invalid @enderror"
                       id="roles_nama" name="roles_nama" value="{{ old('roles_nama', $role->roles_nama) }}" required>
                @error('roles_nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="roles_deskripsi">Deskripsi</label>
                <textarea class="form-control @error('roles_deskripsi') is-invalid @enderror"
                          id="roles_deskripsi" name="roles_deskripsi" required>{{ old('roles_deskripsi', $role->roles_deskripsi) }}</textarea>
                @error('roles_deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-default">Batal</a>
        </form>
    </div>
</div>
@endsection

