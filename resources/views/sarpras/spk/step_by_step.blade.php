@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach ($breadcrumb->list as $item)
                    <li class="breadcrumb-item">{{ $item }}</li>
                @endforeach
            </ol>
            <h1>{{ $breadcrumb->title }}</h1>
        </nav>

        <!-- Pesan Sukses/Error -->
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Tabel Perankingan -->
        <div class="card">
            <div class="card-header">10 Alternatif Terbaik</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fasilitas</th>
                            <th>Ranking</th>
                            <th>Appraisal Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($spkData as $item)
                            <tr>
                                <td>{{ $fasilitasList[$item->Alternatif] ?? $item->Alternatif }}</td>
                                <td>{{ $item->Ranking }}</td>
                                <td>{{ number_format($item->AppraisalScore, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">Tidak ada data perankingan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <p><strong>Rumus Hasil Akhir:</strong> Ranking ditentukan berdasarkan Appraisal Score tertinggi. Appraisal Score dihitung sebagai:
                \( AS = 0.5 \times NSP + 0.5 \times NSN \), di mana NSP dan NSN adalah normalisasi SP dan SN.</p>
            </div>
        </div>

        <!-- Langkah-langkah PSI -->
        <div class="card mt-4">
            <div class="card-header">Langkah-langkah PSI</div>
            <div class="card-body">
                @foreach ($psiSteps as $step => $data)
                    <h5>{{ $data['description'] }}</h5>
                    <pre>{{ print_r($data['data'], true) }}</pre>
                    @php
                        $rumus = '';
                        switch ($step) {
                            case 'step_1_matriks_keputusan':
                                $rumus = 'Matriks Keputusan (A) adalah matriks awal tanpa normalisasi.';
                                break;
                            case 'step_2_matriks_normalisasi':
                                $rumus = 'Normalisasi untuk benefit: \( R_{ij} = \frac{A_{ij}}{\max(A_{j})} \), untuk cost: \( R_{ij} = \frac{\min(A_{j})}{A_{ij}} \).';
                                break;
                            case 'step_2b_total_per_kriteria':
                                $rumus = 'Total per kolom: \( T_{j} = \sum_{i=1}^{m} R_{ij} \).';
                                break;
                            case 'step_3_rata_rata':
                                $rumus = 'Rata-rata per kriteria: \( \bar{R}_{j} = \frac{\sum_{i=1}^{m} R_{ij}}{m} \).';
                                break;
                            case 'step_4_preference_variation':
                                $rumus = 'Preference Variation: \( PV_{j} = \sum_{i=1}^{m} (R_{ij} - \bar{R}_{j})^2 \).';
                                break;
                            case 'step_5_deviation':
                                $rumus = 'Deviation: \( \Phi_{j} = 1 - PV_{j} \).';
                                break;
                            case 'step_6_preference_index':
                                $rumus = 'Preference Index (Bobot): \( \psi_{j} = \frac{\Phi_{j}}{\sum_{j=1}^{n} \Phi_{j}} \).';
                                break;
                        }
                    @endphp
                    @if ($rumus)
                        <p><strong>Rumus:</strong> {{ $rumus }}</p>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Langkah-langkah EDAS -->
        <div class="card mt-4">
            <div class="card-header">Langkah-langkah EDAS</div>
            <div class="card-body">
                @foreach ($edasSteps as $step => $data)
                    <h5>{{ $data['description'] }}</h5>
                    <pre>{{ print_r($data['data'], true) }}</pre>
                    @php
                        $rumus = '';
                        switch ($step) {
                            case 'step_1_matriks_keputusan':
                                $rumus = 'Matriks Keputusan (A) adalah matriks awal tanpa normalisasi.';
                                break;
                            case 'step_2_solusi_rata_rata':
                                $rumus = 'Solusi Rata-rata: \( AVG_{j} = \frac{\sum_{i=1}^{m} A_{ij}}{m} \).';
                                break;
                            case 'step_3_positive_distance':
                                $rumus = 'Positive Distance (PDA): \( PDA_{ij} = \max(0, \frac{A_{ij} - AVG_{j}}{AVG_{j}}) \) untuk benefit, atau \( \max(0, \frac{AVG_{j} - A_{ij}}{AVG_{j}}) \) untuk cost.';
                                break;
                            case 'step_4_negative_distance':
                                $rumus = 'Negative Distance (NDA): \( NDA_{ij} = \max(0, \frac{AVG_{j} - A_{ij}}{AVG_{j}}) \) untuk benefit, atau \( \max(0, \frac{A_{ij} - AVG_{j}}{AVG_{j}}) \) untuk cost.';
                                break;
                            case 'step_5_weighted_positive_distance':
                                $rumus = 'Weighted PDA (SP): \( SP_{i} = \sum_{j=1}^{n} PDA_{ij} \times w_{j} \), di mana \( w_{j} \) adalah bobot dari PSI.';
                                break;
                            case 'step_6_weighted_negative_distance':
                                $rumus = 'Weighted NDA (SN): \( SN_{i} = \sum_{j=1}^{n} NDA_{ij} \times w_{j} \).';
                                break;
                            case 'step_7_total_sp':
                                $rumus = 'Total SP: \( SP_{i,total} = \sum_{j=1}^{n} SP_{ij} \).';
                                break;
                            case 'step_8_total_sn':
                                $rumus = 'Total SN: \( SN_{i,total} = \sum_{j=1}^{n} SN_{ij} \).';
                                break;
                            case 'step_9_normalized_sp':
                                $rumus = 'Normalized SP (NSP): \( NSP_{i} = \frac{SP_{i,total}}{\max(SP_{i,total})} \).';
                                break;
                            case 'step_10_normalized_sn':
                                $rumus = 'Normalized SN (NSN): \( NSN_{i} = 1 - \frac{SN_{i,total}}{\max(SN_{i,total})} \).';
                                break;
                            case 'step_11_appraisal_score':
                                $rumus = 'Appraisal Score: \( AS_{i} = 0.5 \times NSP_{i} + 0.5 \times NSN_{i} \).';
                                break;
                            case 'step_12_hasil_perangkingan':
                                $rumus = 'Ranking: Alternatif diurutkan berdasarkan \( AS_{i} \) dari tertinggi ke terendah.';
                                break;
                        }
                    @endphp
                    @if ($rumus)
                        <p><strong>Rumus:</strong> {{ $rumus }}</p>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- View Satpras (jika admin) -->
        @if (Auth::user()->role_id == 1)
            <div class="card mt-4">
                <div class="card-header">Pratinjau untuk Satpras</div>
                <div class="card-body">
                    {!! $satprasView !!}
                </div>
            </div>
        @endif
    </div>
@endsection
