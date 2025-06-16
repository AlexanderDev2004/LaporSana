@empty($laporan)
    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/laporan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Data Laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="font-weight-bold mb-3">Informasi Laporan</h5>
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right col-3">ID Laporan :</th>
                        <td class="col-9">{{ $laporan->laporan_id }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Pelapor :</th>
                        <td class="col-9">{{ $laporan->user->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Status :</th>
                        <td>
                            <!-- filepath: e:\Software\laragon\www\LaporSana\resources\views\Admin\validasi_laporan\show.blade.php -->
                            <!-- Bagian PHP untuk menentukan badge class -->
                            @php
                                $badgeClass = 'badge badge-secondary';
                                if ($laporan->status_id == 1)
                                    $badgeClass = 'badge badge-warning';
                                else if ($laporan->status_id == 2)
                                    $badgeClass = 'badge badge-danger';
                                else if ($laporan->status_id == 3)
                                    $badgeClass = 'badge badge-primary';
                                else if ($laporan->status_id == 4)
                                    $badgeClass = 'badge badge-success';
                                else if ($laporan->status_id == 5)
                                    $badgeClass = 'badge badge-info';
                            @endphp
                            <span class="{{ $badgeClass }}">{{ $laporan->status->status_nama ?? '-' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Tanggal Melapor :</th>
                        <td class="col-9">{{ $laporan->tanggal_lapor }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Jumlah Pelapor :</th>
                        <td class="col-9">{{ $laporan->jumlah_pelapor }}</td>
                    </tr>
                </table>

                <h5 class="font-weight-bold mt-4 mb-3">Detail Fasilitas</h5>
                @if($laporan->details && count($laporan->details) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Fasilitas</th>
                                    <th>Ruangan</th>
                                    <th>Lantai</th>
                                    <th>Deskripsi</th>
                                    <th>Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($laporan->details as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $detail->fasilitas->fasilitas_nama ?? 'Data tidak tersedia' }}</td>
                                        <td>{{ $detail->fasilitas->ruangan->ruangan_nama ?? 'Data tidak tersedia' }}</td>
                                        <td>{{ $detail->fasilitas->ruangan->lantai->lantai_nama ?? 'Data tidak tersedia' }}</td>
                                        <td>{{ $detail->deskripsi ?? 'Tidak ada deskripsi' }}</td>
                                        <td>
                                            @if($detail->foto_bukti)
                                                <a href="{{ asset('storage/' . $detail->foto_bukti) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $detail->foto_bukti) }}" alt="Bukti"
                                                        class="img-fluid" style="max-height: 80px;">
                                                </a>
                                            @else
                                                <span class="text-muted">Tidak ada bukti</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada detail fasilitas yang dilaporkan.
                    </div>
                @endif

                <!-- Tombol Verifikasi hanya muncul jika status = menunggu verifikasi (1) -->
                @if($laporan->status_id == 1)
                    <div class="text-center mt-4">
                        <button class="btn btn-success mr-2" id="btn-setuju"
                            onclick="konfirmasiVerifikasi('setuju', {{ $laporan->laporan_id }})">
                            <i class="fas fa-check"></i> Setujui
                        </button>
                        <button class="btn btn-danger" id="btn-tolak"
                            onclick="konfirmasiVerifikasi('tolak', {{ $laporan->laporan_id }})">
                            <i class="fas fa-times"></i> Tolak
                        </button>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Script untuk konfirmasi sweetalert -->
    @if($laporan->status_id == 1)
        <script>
            function konfirmasiVerifikasi(tipe, laporanId) {
                let title, text, confirmButtonText, confirmButtonColor;

                if (tipe === 'setuju') {
                    title = 'Setujui Laporan?';
                    text = 'Laporan akan disetujui dan status akan diubah menjadi "Disetujui"';
                    confirmButtonText = 'Ya, Setujui!';
                    confirmButtonColor = '#28a745';
                } else {
                    title = 'Tolak Laporan?';
                    text = 'Laporan akan ditolak dan status akan diubah menjadi "Ditolak"';
                    confirmButtonText = 'Ya, Tolak!';
                    confirmButtonColor = '#dc3545';
                }

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: confirmButtonColor,
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Kirim request AJAX ke controller dengan URL yang benar
                        $.ajax({
                            url: "{{ route('admin.validasi_laporan.verify', $laporan->laporan_id) }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                verifikasi: tipe === 'setuju' ? 'setuju' : 'tolak'
                            },
                            success: function (response) {
                                if (response.status) {
                                    Swal.fire(
                                        'Berhasil!',
                                        response.message,
                                        'success'
                                    ).then(() => {
                                        // Close the modal using the correct ID from index.blade.php
                                        $('#myModal').modal('hide');

                                        // Refresh datatable using the correct variable from index.blade.php
                                        if (typeof dataLaporan !== 'undefined') {
                                            dataLaporan.ajax.reload();
                                        } else {
                                            // Fallback if dataLaporan is not available in this context
                                            $('#table_laporan').DataTable().ajax.reload();
                                        }
                                    });
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error(xhr.responseText);  // Log any error messages for debugging
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan pada server. Detail: ' + error,
                                    'error'
                                );
                            }
                        });
                    }
                });
            }
        </script>
    @endif
@endempty