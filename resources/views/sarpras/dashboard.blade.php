@extends('layouts.sarpras.template')

@section('content')

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Halo, ini dashboard LaporSana!</h3>
    <div class="card-tools"></div>
  </div>

  <div class="card-body">
    <p>Selamat datang! <strong>{{ Auth::user()->name }}</strong>,</p>
    <p>Ngetes dashboard sarpras</p>
  </div>
</div>

@endsection
