@extends('layouts.admin.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Role</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <td>{{ $role->roles_id }}</td>
                </tr>
                <tr>
                    <th>Nama Role</th>
                    <td>{{ $role->roles_nama }}</td>
                </tr>
                <tr>
                    <th>Kode Role</th>
                    <td>{{ $role->roles_kode }}</td>
                </tr>
                <tr>
                    <th>Deskripsi</th>
                    <td>{{ $role->roles_deskripsi ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td>{{ $role->created_at ? $role->created_at->format('d F Y H:i') : '-' }}</td>
                </tr>
                <tr>
                    <th>Diupdate Terakhir</th>
                    <td>{{ $role->updated_at ? $role->updated_at->format('d F Y H:i') : '-' }}</td>
                </tr>
            </table>
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('admin.roles.edit', $role->roles_id) }}" class="btn btn-primary float-right">
                <i class="fas fa-edit"></i> Edit Role
            </a>
        </div>
    </div>
@endsection
