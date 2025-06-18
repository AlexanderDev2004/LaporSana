@extends('layouts.sarpras.template')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>Langkah-langkah SPK</h1>
        </section>
        <section class="content">
            <div class="card">
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <h3>Metode PSI</h3>
                    @foreach($psiSteps as $step)
                        <div class="card mb-3">
                            <div class="card-header">
                                Langkah {{ $step['step'] }}: {{ $step['description'] }}
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            @foreach(array_keys($step['data'][array_key_first($step['data'])]) as $header)
                                                <th>{{ $header }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($step['data'] as $rowKey => $row)
                                            <tr>
                                                <td>{{ $rowKey }}</td>
                                                @foreach($row as $value)
                                                    <td>{{ number_format($value, 4) }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <h3>Metode EDAS</h3>
                    @foreach($edasSteps as $step)
                        <div class="card mb-3">
                            <div class="card-header">
                                Langkah {{ $step['step'] }}: {{ $step['description'] }}
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            @foreach(array_keys($step['data'][array_key_first($step['data'])]) as $header)
                                                <th>{{ $header }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($step['data'] as $rowKey => $row)
                                            <tr>
                                                <td>{{ $rowKey }}</td>
                                                @foreach($row as $value)
                                                    <td>{{ number_format($value, 4) }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
