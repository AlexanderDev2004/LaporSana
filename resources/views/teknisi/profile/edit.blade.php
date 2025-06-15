@extends('layouts.teknisi.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Profil Saya</h3>
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

        <form action="{{ route('teknisi.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username', $user->username) }}" readonly>
                {{-- Username biasanya tidak bisa diubah sendiri, jadi readonly --}}
            </div>

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- <div class="form-group">
                <label for="NIM">NIM (Opsional)</label>
                <input type="text" id="NIM" name="NIM"
                       class="form-control @error('NIM') is-invalid @enderror"
                       value="{{ old('NIM', $user->NIM) }}">
                @error('NIM')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div> --}}

            <div class="form-group">
                <label for="NIP">NIP (Opsional)</label>
                <input type="text" id="NIP" name="NIP"
                       class="form-control @error('NIP') is-invalid @enderror"
                       value="{{ old('NIP', $user->NIP) }}">
                @error('NIP')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="avatar">Avatar</label>
                <input type="file" id="avatar" name="avatar"
                       class="form-control-file @error('avatar') is-invalid @enderror">
                @error('avatar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                @if($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" width="100" class="mt-2 rounded-circle">
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('teknisi.profile.show') }}" class="btn btn-warning">Batal</a>
        </form>
    </div>
</div>
@endsection
