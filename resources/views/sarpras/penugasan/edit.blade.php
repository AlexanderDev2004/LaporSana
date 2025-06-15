<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <form action="{{ route('sarpras.penugasan.update', ['tugas_id' => $tugas->tugas_id]) }}" method="POST" id="form-edit-tugas">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Penugasan #{{ $tugas->tugas_id }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Pilih Teknisi</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">- Pilih Teknisi -</option>
                        @foreach($teknisi as $t)
                            <option value="{{ $t->user_id }}" {{ $t->user_id == $tugas->user_id ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Jenis Tugas</label>
                    <select name="tugas_jenis" class="form-control" required>
                        <option value="">- Pilih Jenis Tugas -</option>
                        <option value="Pemeriksaan" {{ $tugas->tugas_jenis == 'Pemeriksaan' ? 'selected' : '' }}>Pemeriksaan</option>
                        <option value="Perbaikan" {{ $tugas->tugas_jenis == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                    </select>
                </div>
                <hr>
                <p class="font-weight-bold">Detail Lokasi & Fasilitas (Tidak dapat diubah)</p>
                <div class="alert alert-info">
                    <strong>Fasilitas:</strong> {{ $tugas->details->first()->fasilitas->fasilitas_nama ?? 'N/A' }} <br>
                    <strong>Lokasi:</strong> {{ $tugas->details->first()->fasilitas->ruangan->lantai->lantai_nama ?? 'N/A' }} - {{ $tugas->details->first()->fasilitas->ruangan->ruangan_nama ?? 'N/A' }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#form-edit-tugas').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST', // Walaupun methodnya PUT, form HTML & AJAX seringkali tetap POST
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.status) {
                    $('#myModal').modal('hide');
                    Swal.fire('Berhasil!', response.message, 'success');
                    $('#table_tugas').DataTable().ajax.reload();
                } else {
                    Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Gagal menyimpan perubahan.', 'error');
            }
        });
    });
});
</script>
