<form action="{{ route('pelapor.store') }}" method="POST" id="form-tambah" enctype="multipart/form-data"> 
    @csrf 
    <div class="modal-content">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif 
        <div class="modal-body"> 
            <div class="form-group"> 
                <label>Lantai</label> 
                <select name="lantai_id" id="lantai_id" class="form-control" required> 
                    <option value="">- Pilih Lantai -</option> 
                    @foreach($lantai as $k) 
                        <option value="{{ $k->lantai_id }}">{{ $k->lantai_nama }}</option> 
                    @endforeach 
                </select> 
                @error('lantai_id')
                <small class="form-text text-danger">{{ $message }}</small>
                @enderror 
            </div>
            <div class="form-group"> 
                <label>Ruangan</label> 
                <select name="ruangan_id" id="ruangan_id" class="form-control" required> 
                    <option value="">- Pilih Ruangan -</option> 
                    @foreach($ruangan as $k) 
                        <option value="{{ $k->ruangan_id }}">{{ $k->ruangan_nama }}</option> 
                    @endforeach 
                </select> 
                @error('ruangan_id')
                <small class="form-text text-danger">{{ $message }}</small>
                @enderror 
            </div>
            <div class="form-group"> 
                <label>Fasilitas</label> 
                <select name="fasilitas_id" id="fasilitas_id" class="form-control" required> 
                    <option value="">- Pilih Fasilitas -</option> 
                    @foreach($fasilitas as $k) 
                        <option value="{{ $k->fasilitas_id }}">{{ $k->fasilitas_nama }}</option> 
                    @endforeach 
                </select> 
                @error('fasilitas_id')
                <small class="form-text text-danger">{{ $message }}</small>
                @enderror 
            </div> 
            <div class="form-group"> 
                <label>Foto Bukti</label> 
                <input type="file" name="foto_bukti" id="foto_bukti" class="form-control-file"> 
                <small id="error-foto_bukti" class="error-text form-text text-danger"></small> 
            </div> 
            <div class="form-group"> 
                <label>Deskripsi</label> 
                <input value="" type="text" name="deskripsi" id="deskripsi" class="form-control" required> 
                <small id="error-deskripsi" class="error-text form-text text-danger"></small> 
            </div>
        </div>
        <div class="modal-footer"> 
            <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button> 
        </div> 
    </div>  
</form>