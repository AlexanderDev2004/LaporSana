@extends('layouts.admin.template')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Step-by-Step SPK</h3>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="accordion" id="spkAccordion">
                @if (!empty($psiSteps))
                    @foreach ($psiSteps as $stepName => $data)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingPsi{{ $loop->index }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePsi{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapsePsi{{ $loop->index }}">
                                    {{ $stepName }}
                                </button>
                            </h2>
                            <div id="collapsePsi{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="headingPsi{{ $loop->index }}" data-bs-parent="#spkAccordion">
                                <div class="accordion-body">
                                    @if (is_array($data) && !empty($data))
                                        @if (in_array($stepName, ['Matriks Keputusan (PSI)', 'Matriks Normalisasi']))
                                            <table class="table table-bordered table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        @foreach (array_keys($data[0]) as $key)
                                                            <th>{{ $key }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $row)
                                                        <tr>
                                                            @foreach ($row as $value)
                                                                <td>{{ number_format($value, 4) }}</td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <table class="table table-bordered table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Kriteria</th>
                                                        <th>Nilai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $key => $value)
                                                        <tr>
                                                            <td>{{ $key }}</td>
                                                            <td>{{ number_format($value, 4) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    @else
                                        <p>Tidak ada data untuk langkah ini.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if (!empty($edasSteps))
                    @foreach ($edasSteps as $stepName => $data)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEdas{{ $loop->index }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEdas{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapseEdas{{ $loop->index }}">
                                    {{ $stepName }}
                                </button>
                            </h2>
                            <div id="collapseEdas{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="headingEdas{{ $loop->index }}" data-bs-parent="#spkAccordion">
                                <div class="accordion-body">
                                    @if (is_array($data) && !empty($data))
                                        @if (in_array($stepName, ['Matriks Keputusan (EDAS)', 'PDA', 'NDA', 'SP (Weighted PDA)', 'SN (Weighted NDA)', 'Hasil Perangkingan Akhir']))
                                            <table class="table table-bordered table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        @foreach (array_keys($data[0]) as $key)
                                                            <th>{{ $key }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $row)
                                                        <tr>
                                                            @foreach ($row as $value)
                                                                <td>{{ number_format($value, 4) }}</td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <table class="table table-bordered table-hover">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Kriteria</th>
                                                        <th>Nilai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $key => $value)
                                                        <tr>
                                                            <td>{{ $key }}</td>
                                                            <td>{{ number_format($value, 4) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    @else
                                        <p>Tidak ada data untuk langkah ini.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Display SPK Ranking -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Rekomendasi Perbaikan (SPK)</h3>
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
                            @if (!empty($spkData))
                                @foreach ($spkData as $i => $item)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $fasilitasList[$item['Alternatif']] ?? 'Nama Tidak Ditemukan' }}</td>
                                        <td>{{ number_format($item['AppraisalScore'], 4) }}</td>
                                        <td>{{ $item['Ranking'] }}</td>
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
@endsection
