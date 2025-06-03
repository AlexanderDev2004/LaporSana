@extends('layouts.pelapor.template')

@section('content')
    <form action="{{ url('/dosen/store') }}" method="POST" id="form-tambah"> 
    @csrf 
    <div class="modal-content"> 
        <div class="modal-header"> 
            <h5 class="modal-title" id="exampleModalLabel">Tambah Laporan Kerusakan Fasilitas</h5> 
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
        </div> 
        <div class="modal-body"> 
            <div class="form-group"> 
                <label>Lantai</label> 
                <select name="lantai_id" id="lantai_id" class="form-control" required> 
                    <option value="">- Pilih Lantai -</option> 
                    @foreach($lantai as $k) 
                        <option value="{{ $k->lantai_id }}">{{ $k->lantai_nama }}</option> 
                    @endforeach 
                </select> 
                <small id="error-lantai_id" class="error-text form-text text-danger"></small> 
            </div>
            <div class="form-group"> 
                <label>Ruangan</label> 
                <select name="ruangan_id" id="ruangan_id" class="form-control" required> 
                    <option value="">- Pilih Ruangan -</option> 
                    @foreach($ruangan as $k) 
                        <option value="{{ $k->ruangan_id }}">{{ $k->ruangan_nama }}</option> 
                    @endforeach 
                </select> 
                <small id="error-ruangan_id" class="error-text form-text text-danger"></small> 
            </div>
            <div class="form-group"> 
                <label>Fasilitas</label> 
                <select name="fasilitas_id" id="fasilitas_id" class="form-control" required> 
                    <option value="">- Pilih Fasilitas -</option> 
                    @foreach($fasilitas as $k) 
                        <option value="{{ $k->fasilitas_id }}">{{ $k->fasilitas_nama }}</option> 
                    @endforeach 
                </select> 
                <small id="error-fasilitas_id" class="error-text form-text text-danger"></small> 
            </div> 
            <div class="form-group"> 
                <label>Foto Bukti</label> 
                <input type="file" name="foto_bukti" id="foto_bukti" class="form-control-file" required> 
                <small id="error-foto_bukti" class="error-text form-text text-danger"></small> 
            </div> 
            <div class="form-group"> 
                <label>Deskripsi</label> 
                <input value="" type="text" name="deskripsi" id="deskripsi" class="form-control" required> 
                <small id="error-deskripsi" class="error-text form-text text-danger"></small> 
            </div> 
        <div class="modal-footer"> 
            <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button> 
            <button type="submit" class="btn btn-primary">Simpan</button> 
        </div> 
    </div>  
</form>
@endsection