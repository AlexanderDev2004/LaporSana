@extends('layouts.admin.template')

@section('content')
<div class="container-fluid">
    <h2 class="my-4">Detail Perhitungan SPK (Full Columns)</h2>

    <!-- Tab Navigasi -->
    <ul class="nav nav-tabs" id="spkTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="ranking-tab" data-toggle="tab" href="#ranking">Hasil Ranking</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="psi-tab" data-toggle="tab" href="#psi">Metode PSI</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="edas-tab" data-toggle="tab" href="#edas">Metode EDAS</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="raw-tab" data-toggle="tab" href="#raw">Data Awal</a>
        </li>
    </ul>

    <!-- Konten Tab -->
    <div class="tab-content" id="spkTabsContent">
        <!-- Tab 1: Hasil Ranking -->
        <div class="tab-pane fade show active" id="ranking">
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <h5>Hasil Perangkingan EDAS</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Ranking</th>
                                    <th>Alternatif (ID)</th>
                                    <th>Appraisal Score</th>
                                    @foreach($weights as $kriteria => $bobot)
                                        <th>{{ $kriteria }} (Bobot: {{ number_format($bobot, 4) }})</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ranking as $item)
                                <tr>
                                    <td>{{ $item['Ranking'] }}</td>
                                    <td>{{ $item['Alternatif'] }}</td>
                                    <td>{{ number_format($item['AppraisalScore'], 4) }}</td>
                                    @foreach($weights as $kriteria => $bobot)
                                        <td>{{ $raw_data[$loop->parent->index][$kriteria] }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Metode PSI -->
        <div class="tab-pane fade" id="psi">
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5>Langkah PSI (Penentuan Bobot Kriteria)</h5>
                </div>
                <div class="card-body">
                    <h6>1. Matriks Normalisasi (R)</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Alternatif</th>
                                    @foreach($weights as $kriteria => $bobot)
                                        <th>{{ $kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($steps['psi']['normalization'] as $item)
                                <tr>
                                    <td>{{ $item['Alternatif'] }}</td>
                                    @foreach($weights as $kriteria => $bobot)
                                        <td>{{ number_format($item[$kriteria], 6) }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h6 class="mt-4">2. Nilai Rata-rata (Ēₖ)</h6>
                    <pre>{{ json_encode($steps['psi']['means'], JSON_PRETTY_PRINT) }}</pre>

                    <h6 class="mt-4">3. Preference Variation (PVₖ)</h6>
                    <pre>{{ json_encode($steps['psi']['pv'], JSON_PRETTY_PRINT) }}</pre>

                    <h6 class="mt-4">4. Bobot Kriteria (ψₖ)</h6>
                    <pre>{{ json_encode($weights, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>

        <!-- Tab 3: Metode EDAS -->
        <div class="tab-pane fade" id="edas">
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h5>Langkah EDAS (Perangkingan)</h5>
                </div>
                <div class="card-body">
                    <h6>1. Solusi Rata-rata (AVG)</h6>
                    <pre>{{ json_encode($steps['edas']['average_solution'], JSON_PRETTY_PRINT) }}</pre>

                    <h6 class="mt-4">2. Positive Distance (PDA)</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Alternatif</th>
                                    @foreach($weights as $kriteria => $bobot)
                                        <th>{{ $kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($steps['edas']['pda'] as $altId => $pdaValues)
                                <tr>
                                    <td>{{ $altId }}</td>
                                    @foreach($pdaValues as $value)
                                        <td>{{ number_format($value, 6) }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h6 class="mt-4">3. Weighted SP</h6>
                    <pre>{{ json_encode($steps['edas']['weighted_sp'], JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
        </div>

        <!-- Tab 4: Data Awal -->
        <div class="tab-pane fade" id="raw">
            <div class="card mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5>Data Mentah Awal</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Alternatif</th>
                                    @foreach($weights as $kriteria => $bobot)
                                        <th>{{ $kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($raw_data as $item)
                                <tr>
                                    <td>{{ $item['Alternatif'] }}</td>
                                    @foreach($weights as $kriteria => $bobot)
                                        <td>{{ $item[$kriteria] }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Bootstrap Tab -->
<script>
$(document).ready(function() {
    $('#spkTabs a').on('click', function(e) {
        e.preventDefault();
        $(this).tab('show');
    });
});
</script>

<style>
.table-responsive {
    max-height: 500px;
    overflow-y: auto;
}
pre {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
}
</style>
@endsection
