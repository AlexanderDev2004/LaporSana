@extends('layouts.admin.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tambah Role Baru</h3>
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

        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="roles_nama">Nama Role</label>
                <input type="text" class="form-control @error('roles_nama') is-invalid @enderror"
                       id="roles_nama" name="roles_nama" value="{{ old('roles_nama') }}" required>
                @error('roles_nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="roles_kode">Kode Role</label>
                <input type="text" class="form-control @error('roles_kode') is-invalid @enderror"
                       id="roles_kode" name="roles_kode" value="{{ old('roles_kode') }}" required>
                @error('roles_kode')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

           

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-default">Batal</a>
        </form>
    </div>
</div>
@endsection
