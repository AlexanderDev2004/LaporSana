@extends('layouts.admin.template')
@section('content')
<div class="modal-header">
    <h5 class="modal-title">Detail Laporan #{{ $laporan->laporan_id }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
</div>

<div class="modal-body">
    <p><strong>Nama Pelapor:</strong> {{ $laporan->user->nama ?? '-' }}</p>
    <p><strong>Status:</strong> {{ $laporan->status->status_nama ?? '-' }}</p>
    <p><strong>Tanggal Lapor:</strong> {{ $laporan->tanggal_lapor->format('d-m-Y H:i') }}</p>

    <hr>
    <h6>Detail Kerusakan</h6>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Fasilitas</th>
                <th>Deskripsi</th>
                <th>Foto</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan->details as $detail)
                <tr>
                    <td>{{ $detail->fasilitas->fasilitas_nama ?? '-' }}</td>
                    <td>{{ $detail->deskripsi }}</td>
                    <td>
                        @if($detail->foto_bukti)
                            <img src="{{ asset('storage/' . $detail->foto_bukti) }}" width="100" alt="Foto Bukti">
                        @else
                            <em>Tidak ada foto</em>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="modal-footer">
    @if($laporan->status_id == config('constants.status_menunggu'))
        <button type="button" class="btn btn-success" onclick="verifyLaporan('{{ $laporan->laporan_id }}', 'setujui')">Setujui</button>
        <button type="button" class="btn btn-danger" onclick="verifyLaporan('{{ $laporan->laporan_id }}', 'tolak')">Tolak</button>
    @endif
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function verifyLaporan(laporanId, action) {
        Swal.fire({
            title: `Apakah Anda yakin ingin ${action == 'setujui' ? 'menyetujui' : 'menolak'} laporan ini?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("admin/laporan/verify") }}/' + laporanId,
                    method: 'POST',
                    data: {
                        verifikasi: action,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message,
                            }).then(() => {
                                $('#laporanModal').modal('hide');
                                $('#laporanTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat memproses laporan.',
                        });
                    }
                });
            }
        });
    }
</script>
@endsection
