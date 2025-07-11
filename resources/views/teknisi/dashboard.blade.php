@extends('layouts.teknisi.template')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 p-3">

                {{-- <!-- Judul -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Dashboard Teknisi</h5>
                </div> --}}

                <!-- Statistik -->
                <div class="row mb-3">

                    <!-- Tugas Terbaru -->
                    <div class="col-lg-6 mb-3">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white py-2">
                                    <h6 class="mb-0">Tugas Terbaru</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="py-2">Fasilitas</th>
                                                    <th class="py-2">Tanggal Penugasan</th>
                                                    <th class="py-2">Detail Tugas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($tugasTerbaru as $tugas)
                                                    <tr>
                                                        <td>
                                                            @if($tugas->details->count())
                                                                <ul class="mb-0 list-unstyled">
                                                                    @foreach ($tugas->details as $detail)
                                                                        <li>{{ $detail->fasilitas->fasilitas_nama ?? 'Tidak ada fasilitas' }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <span class="text-muted">Tidak ada fasilitas</span>
                                                            @endif
                                                        </td>
                                                        <td class="py-2">
                                                            {{ \Carbon\Carbon::parse($tugas->tugas_mulai)->format('d-m-Y') }}
                                                        </td>
                                                        <td class="py-2">
                                                            <a href="#" class="text-primary medium"
                                                               onclick="modalAction('{{ route('teknisi.show', $tugas->tugas_id) }}'); return false;">
                                                                Detail Tugas
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center py-2">Belum ada tugas terbaru.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grafik Kerusakan -->
                    <div class="col-lg-6 mb-3">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white py-2">
                                    <h6 class="mb-0">Kerusakan Bulanan</h6>
                                </div>
                                <div class="card-body p-3" style="height: 250px;">
                                    <canvas id="damageChart" style="height: 200px !important;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal untuk AJAX -->
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Fungsi untuk load detail tugas ke modal
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        // Grafik Kerusakan Bulanan
        const dataStatistik = @json(array_values($dataStatistik)); // array 12 bulan

        const ctx = document.getElementById('damageChart').getContext('2d');
        const damageChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah Kerusakan',
                    data: dataStatistik,
                    backgroundColor: '#2196f3',
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 10
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
@endsection
