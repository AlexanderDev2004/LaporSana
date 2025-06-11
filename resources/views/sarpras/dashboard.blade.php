@extends('layouts.sarpras.template')

@section('content')

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Halo, ini dashboard LaporSana!</h3>
    <div class="card-tools"></div>
  </div>

  <div class="card-body">
    <p>Selamat datang! <strong>{{ Auth::user()->name }}</strong>,</p>
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

@endsection
