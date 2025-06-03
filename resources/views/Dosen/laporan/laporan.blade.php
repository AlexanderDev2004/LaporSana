@extends('layouts.pelapor.template')

@section('content')

<style>
    #table_laporan {
    border: 1px solid #ffffff;
    }

    #table_laporan th {
        border-color: #ffffff;
    }
</style>

<div class="card card-outline card-warning">
  <div class="card-header">
    <h3 class="card-title">{{ $page->title }}</h3>
    <div class="card-tools">
          <a href="{{ route('dosen.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Laporan</a>
    </div>
  </div>
  <div class="card-body"> 
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table table-bordered table-striped table-hover table-sm" id="table_laporan"> 
          <thead> 
            <tr>
                <th>ID</th>
                <th>Fasilitas</th>
                <th>Ruangan</th>
                <th>Lantai</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr> 
          </thead> 
      </table>
    </div>
</div>
@endsection