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
                {{-- Langkah-langkah PSI --}}
                <h1>Langkah-langkah PSI</h1>
            </br>
                @if (!empty($psiSteps))
                    @foreach ($psiSteps as $stepName => $stepContent)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingPsi{{ $loop->index }}">
                                <button class="accordion-button" type="button" {{-- Removed 'collapsed' class --}}
                                    data-bs-toggle="collapse" data-bs-target="#collapsePsi{{ $loop->index }}"
                                    aria-expanded="true" {{-- Always expanded --}}
                                    aria-controls="collapsePsi{{ $loop->index }}">
                                    {{ $stepName }}
                                </button>
                            </h2>
                            <div id="collapsePsi{{ $loop->index }}"
                                class="accordion-collapse collapse show" {{-- Added 'show' class --}}
                                aria-labelledby="headingPsi{{ $loop->index }}" data-bs-parent="#spkAccordion">
                                <div class="accordion-body">
                                    {{-- Display Description --}}
                                    @if (isset($stepContent['description']))
                                        <p><strong>Description:</strong> {{ $stepContent['description'] }}</p>
                                    @endif

                                    {{-- Display Data as Table --}}
                                    @if (isset($stepContent['data']))
                                        @php $data = $stepContent['data']; @endphp
                                        @if (is_array($data) && !empty($data))
                                            @if (isset($data[0]) && is_array($data[0]))
                                                {{-- Table for array of associative arrays (records) --}}
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead>
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
                                                                        <td>
                                                                            @if (is_numeric($value))
                                                                                {{ number_format($value, 4) }}
                                                                            @elseif(is_array($value) || is_object($value))
                                                                                {{-- Handle nested arrays/objects by encoding to JSON string --}}
                                                                                {{ json_encode($value) }}
                                                                            @else
                                                                                {{ $value }}
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                {{-- Table for associative array (key-value pairs) --}}
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Kriteria</th>
                                                                <th>Nilai</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($data as $key => $value)
                                                                <tr>
                                                                    <td>{{ $key }}</td>
                                                                    <td>
                                                                        @if (is_numeric($value))
                                                                            {{ number_format($value, 4) }}
                                                                        @elseif(is_array($value) || is_object($value))
                                                                            {{-- Handle nested arrays/objects by encoding to JSON string --}}
                                                                            {{ json_encode($value) }}
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        @else
                                            <p>Tidak ada data untuk langkah ini.</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                </br>
                <h1>Langkah-langkah EDAS</h1>
                </br>
                @if (!empty($edasSteps))
                    @foreach ($edasSteps as $stepName => $stepContent)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEdas{{ $loop->index }}">
                                <button class="accordion-button" type="button" {{-- Removed 'collapsed' class --}}
                                    data-bs-toggle="collapse" data-bs-target="#collapseEdas{{ $loop->index }}"
                                    aria-expanded="true" {{-- Always expanded --}}
                                    aria-controls="collapseEdas{{ $loop->index }}">
                                    {{ $stepName }}
                                </button>
                            </h2>
                            <div id="collapseEdas{{ $loop->index }}"
                                class="accordion-collapse collapse show" {{-- Added 'show' class --}}
                                aria-labelledby="headingEdas{{ $loop->index }}" data-bs-parent="#spkAccordion">
                                <div class="accordion-body">
                                    {{-- Display Description --}}
                                    @if (isset($stepContent['description']))
                                        <p><strong>Description:</strong> {{ $stepContent['description'] }}</p>
                                    @endif

                                    {{-- Display Data as Table --}}
                                    @if (isset($stepContent['data']))
                                        @php $data = $stepContent['data']; @endphp
                                        @if (is_array($data) && !empty($data))
                                            @if (isset($data[0]) && is_array($data[0]))
                                                {{-- Table for array of associative arrays (records) --}}
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead>
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
                                                                        <td>
                                                                            @if (is_numeric($value))
                                                                                {{ number_format($value, 4) }}
                                                                            @elseif(is_array($value) || is_object($value))
                                                                                {{-- Handle nested arrays/objects by encoding to JSON string --}}
                                                                                {{ json_encode($value) }}
                                                                            @else
                                                                                {{ $value }}
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                {{-- Table for associative array (key-value pairs) --}}
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Kriteria</th>
                                                                <th>Nilai</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($data as $key => $value)
                                                                <tr>
                                                                    <td>{{ $key }}</td>
                                                                    <td>
                                                                        @if (is_numeric($value))
                                                                            {{ number_format($value, 4) }}
                                                                        @elseif(is_array($value) || is_object($value))
                                                                            {{-- Handle nested arrays/objects by encoding to JSON string --}}
                                                                            {{ json_encode($value) }}
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        @else
                                            <p>Tidak ada data untuk langkah ini.</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Tabel Ranking --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Rekomendasi Perbaikan (SPK)</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
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
                                        <td>
                                            @php
                                                $alt = $item['Alternatif'];
                                                if (is_array($alt)) {
                                                    $alt = reset($alt);
                                                }
                                                $namaFasilitas = $fasilitasList[$alt] ?? 'Nama Tidak Ditemukan';
                                            @endphp
                                            {{ $namaFasilitas }}
                                        </td>
                                        <td>
                                            @php $score = $item['AppraisalScore']; @endphp
                                            @if (is_array($score))
                                                {{ implode(', ', array_map('strval', array_values($score))) }}
                                            @else
                                                {{ number_format($score, 4) }}
                                            @endif
                                        </td>
                                        <td>
                                            @php $rank = $item['Ranking']; @endphp
                                            @if (is_array($rank))
                                                {{ implode(', ', array_map('strval', array_values($rank))) }}
                                            @else
                                                {{ $rank }}
                                            @endif
                                        </td>
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
