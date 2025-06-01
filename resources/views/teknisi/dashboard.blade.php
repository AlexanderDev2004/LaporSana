@extends('layouts.teknisi.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 p-3">

            <!-- Judul -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Dashboard Teknisi</h5>
            </div>

            <!-- Statistik -->
            <div class="row mb-3">
                {{-- <!-- Laporan -->
                <div class="col-md-6 mb-3">
                    <div class="p-3 bg-white shadow-sm border rounded d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Laporan</div>
                            <h4 class="mb-0">
                                3,782 
                                <small class="text-success fs-6">
                                    <i class="fas fa-arrow-up text-primary"></i> 11.01%
                                </small>
                            </h4>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-lg text-secondary"></i>
                        </div>
                    </div>
                </div> --}}

                <!-- Perbaikan -->
                {{-- <div class="col-md-6 mb-3">
                    <div class="p-3 bg-white shadow-sm border rounded d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Perbaikan</div>
                            <h4 class="mb-0">
                                5,359 
                                <small class="text-danger fs-6">
                                    <i class="fas fa-arrow-down text-primary"></i> 9.05%
                                </small>
                            </h4>
                        </div>
                        <div>
                            <i class="fas fa-tools fa-lg text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div> --}}

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
                                            <th class="py-2">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="py-2">Proyektor</td>
                                            <td class="py-2">01-03-2025</td>
                                            <td class="py-2">
                                                <a href="#" class="text-primary small">Detail Laporan</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-2">WC Toilet Pria</td>
                                            <td class="py-2">02-04-2025</td>
                                            <td class="py-2">
                                                <a href="#" class="text-primary small">Detail Laporan</a>
                                            </td>
                                        </tr>
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
                        <div class="card-body p-3">
                            <canvas id="damageChart" style="height: 200px !important;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('damageChart').getContext('2d');
    const damageChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Jumlah Kerusakan',
                data: [100, 200, 450, 300, 0, 0, 0, 0, 0, 0, 0, 0],
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
            }
        }
    });
</script>
@endsection
