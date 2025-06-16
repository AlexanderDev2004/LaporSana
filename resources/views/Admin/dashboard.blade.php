@extends('layouts.admin.template')

@section('content')
    <!-- Statistics Cards dengan padding yang sudah dikurangi dan diatur ke tengah -->
    <div class="card mb-4">
        <!-- Mengurangi padding atas card body dan menambahkan flex utilities -->
        <div class="card-body p-0 py-2">
            <div id="card-container" class="d-flex justify-content-center overflow-hidden position-relative">
                <!-- Menggunakan justify-content-center untuk posisi tengah -->
                <div id="card-wrapper" class="d-flex" style="cursor: grab; max-width: 100%;">
                    <!-- Total Laporan -->
                    <div class="flex-shrink-0 mx-2" style="width: 230px;">
                        <!-- Mengurangi min-width dan menambahkan fixed width + mx-2 untuk margin kiri-kanan -->
                        <div class="small-box bg-white shadow-sm">
                            <div class="card-header bg-secondary text-white py-2">
                                <!-- Menambahkan py-2 untuk mengurangi padding atas bawah -->
                                <h6 class="m-0">Total Laporan</h6>
                            </div>
                            <div class="inner p-3">
                                <!-- Menambahkan padding yang lebih kecil -->
                                <div class="d-flex align-items-center mb-2">
                                    <!-- Mengurangi margin bottom -->
                                    <div class="bg-light rounded-circle p-2 mr-2">
                                        <!-- Mengurangi padding dan margin right -->
                                        <i class="fas fa-file-alt text-secondary"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-1">Total Laporan</p>
                                <h2 class="mb-0">{{ $card_data['total_laporan'] }}</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan belum diputuskan -->
                    <div class="flex-shrink-0 mx-2" style="width: 230px;">
                        <div class="small-box bg-white shadow-sm">
                            <div class="card-header bg-warning text-white py-2">
                                <h6 class="m-0">Belum Diputuskan</h6>
                            </div>
                            <div class="inner p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-light rounded-circle p-2 mr-2">
                                        <i class="fas fa-clock text-warning"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-1">Total belum diputuskan</p>
                                <h2 class="mb-0">{{ $card_data['menunggu_verifikasi'] }}</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Dalam Proses Perbaikan -->
                    <div class="flex-shrink-0 mx-2" style="width: 230px;">
                        <div class="small-box bg-white shadow-sm">
                            <div class="card-header bg-primary text-white py-2">
                                <h6 class="m-0">Proses Perbaikan</h6>
                            </div>
                            <div class="inner p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-light rounded-circle p-2 mr-2">
                                        <i class="fas fa-tools text-primary"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-1">Total dalam Proses Perbaikan</p>
                                <h2 class="mb-0">{{ $card_data['diproses'] }}</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Laporan Ditolak -->
                    <div class="flex-shrink-0 mx-2" style="width: 230px;">
                        <div class="small-box bg-white shadow-sm">
                            <div class="card-header bg-danger text-white py-2">
                                <h6 class="m-0">Laporan Ditolak</h6>
                            </div>
                            <div class="inner p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-light rounded-circle p-2 mr-2">
                                        <i class="fas fa-times-circle text-danger"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-1">Total Ditolak</p>
                                <h2 class="mb-0">{{ $card_data['ditolak'] }}</h2>
                            </div>
                        </div>
                    </div>

                    <!-- Selesai -->
                    <div class="flex-shrink-0 mx-2" style="width: 230px;">
                        <div class="small-box bg-white shadow-sm">
                            <div class="card-header bg-success text-white py-2">
                                <h6 class="m-0">Selesai</h6>
                            </div>
                            <div class="inner p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-light rounded-circle p-2 mr-2">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                </div>
                                <p class="text-muted mb-1">Total Selesai</p>
                                <h2 class="mb-0">{{ $card_data['selesai'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h3 class="card-title">Kerusakan Bulanan</h3>
                </div>
                <div class="card-body" style="min-height: 350px; height: 400px;">
                    <canvas id="damageChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h3 class="card-title">Jumlah Kepuasan</h3>
                </div>
                <div class="card-body" style="min-height: 350px; height: 400px;">
                    <canvas id="satisfactionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1 text-left">
                        <h3 class="card-title mb-0">Rekomendasi Perbaikan (SPK)</h3>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <form action="{{ route('perbarui.data') }}" method="POST" class="mb-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-sync-alt"></i> Perbarui Data
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Fasilitas</th>
                                <th>Score Ranking</th>
                                <th>Ranking</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($spkData) && $spkData->count() > 0)
                                @foreach ($spkData as $i => $item)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $fasilitasList[$item->fasilitas_id] ?? ($item->fasilitas->fasilitas_nama ?? 'Nama Tidak Ditemukan') }}
                                        </td>
                                        <td>{{ number_format($item->score_ranking, 4) }}</td>
                                        <td>{{ $item->rank }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            // Drag functionality for cards
            document.addEventListener('DOMContentLoaded', function() {
                const cardWrapper = document.getElementById('card-wrapper');
                const container = document.getElementById('card-container');
                const monthlyDamageData = @json($monthly_damage_data);
                const satisfactionData = @json($satisfactionData);
                let isDown = false;
                let startX;
                let scrollLeft;

                // Mouse events for dragging
                cardWrapper.addEventListener('mousedown', (e) => {
                    isDown = true;
                    cardWrapper.style.cursor = 'grabbing';
                    startX = e.pageX - cardWrapper.offsetLeft;
                    scrollLeft = container.scrollLeft;
                    e.preventDefault();
                });

                cardWrapper.addEventListener('mouseleave', () => {
                    isDown = false;
                    cardWrapper.style.cursor = 'grab';
                });

                cardWrapper.addEventListener('mouseup', () => {
                    isDown = false;
                    cardWrapper.style.cursor = 'grab';
                });

                cardWrapper.addEventListener('mousemove', (e) => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - cardWrapper.offsetLeft;
                    const walk = (x - startX) * 2; // Speed multiplier
                    container.scrollLeft = scrollLeft - walk;
                });

                // Touch events for mobile
                cardWrapper.addEventListener('touchstart', (e) => {
                    isDown = true;
                    startX = e.touches[0].pageX - cardWrapper.offsetLeft;
                    scrollLeft = container.scrollLeft;
                }, {
                    passive: true
                });

                cardWrapper.addEventListener('touchend', () => {
                    isDown = false;
                });

                cardWrapper.addEventListener('touchmove', (e) => {
                    if (!isDown) return;
                    const x = e.touches[0].pageX - cardWrapper.offsetLeft;
                    const walk = (x - startX) * 2;
                    container.scrollLeft = scrollLeft - walk;
                }, {
                    passive: true
                });

                const chartOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 5
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                    ];
                                    return monthNames[context[0].dataIndex];
                                }
                            }
                        }
                    }
                };

                // Create monthly damage chart
                const damageChart = document.getElementById('damageChart').getContext('2d');
                new Chart(damageChart, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                            'Dec'
                        ],
                        datasets: [{
                            label: 'Jumlah Fasilitas',
                            data: monthlyDamageData,
                            backgroundColor: '#0d6efd',
                            borderWidth: 0,
                            borderRadius: 4
                        }]
                    },
                    options: chartOptions
                });

                // Create satisfaction chart
                const satisfactionChart = document.getElementById('satisfactionChart').getContext('2d');
                new Chart(satisfactionChart, {
                    type: 'bar',
                    data: {
                        labels: ['1', '2', '3', '4', '5'],
                        datasets: [{
                            label: 'Kepuasan',
                            data: satisfactionData,
                            backgroundColor: '#0d6efd',
                            borderWidth: 0,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        ...chartOptions,
                        plugins: {
                            ...chartOptions.plugins,
                            tooltip: {
                                callbacks: {
                                    title: function(context) {
                                        return 'Rating: ' + context[0].label;
                                    },
                                    label: function(context) {
                                        return 'Jumlah: ' + context.raw;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
