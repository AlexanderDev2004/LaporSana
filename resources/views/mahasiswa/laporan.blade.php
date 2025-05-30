@extends('layouts.pelapor.template')

@section('content')

<style>
    #table_kategori {
    border: 1px solid #ffffff; /* Ganti dengan warna yang kamu mau */
    }

    #table_kategori th {
        border-color: #ffffff; /* warna khusus header (opsional) */
    }
</style>

<div class="card card-outline card-warning">
  <div class="card-header">
    <h3 class="card-title">{{ $page->title }}</h3>
    <div class="card-tools">
          <button onclick="modalAction('{{ url('/mahasiswa/create') }}')" class="btn btn-primary">Tambah Laporan</button> 
    </div>
  </div>
  <div class="card-body"> 
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table table-bordered table-striped table-hover table-sm" id="table_kategori"> 
          <thead> 
            <tr>
                <th>ID</th>
                <th>Fasilitas</th>
                <th>Ruangan</th>
                <th>Kelas</th>
                <th>Pelapor</th>
                <th>Aksi</th>
            </tr> 
          </thead> 
      </table>
    </div>
</div>
@endsection

@push('css') 
@endpush 
 
@push('js') 
  <script>
   function modalAction(url = ''){ 
    $('#myModal').load(url,function(){ 
        $('#myModal').modal('show'); 
      }); 
    }
  var dataLaporan; 
    $(document).ready(function() { 
      dataLaporan = $('#table_laporan').DataTable({ 
          // serverSide: true, jika ingin menggunakan server side processing 
          serverSide: true,      
          ajax: { 
              "url": "{{ url('laporan/list') }}", 
              "dataType": "json", 
              "type": "POST",
              "data": function (d) {
                d.laporan_id = $('#laporan_id').val();
              } 
          }, 
          columns: [ 
            {
                 // nomor urut dari laravel datatable addIndexColumn() 
              data: "DT_RowIndex",             
              className: "text-center",
              width: "5%", 
              orderable: false, 
              searchable: false     
            },{ 
              data: "kategori_kode",                
              className: "",
              width: "10%", 
              // orderable: true, jika ingin kolom ini bisa diurutkan  
              orderable: true,     
              // searchable: true, jika ingin kolom ini bisa dicari 
              searchable: true     
            },{ 
              data: "kategori_nama",                
              className: "",
              width: "14%", 
              orderable: true,     
              searchable: true     
            },{ 
              data: "aksi",                
              className: "",
              width: "14%", 
              orderable: false,   // orderable: true, jika ingin kolom ini bisa diurutkan  
              searchable: false   // searchable: ture, jika ingin kolom bisa dicari 
            } 
          ] 
      });
        $('#kategori_id').on('change', function(){
        dataKategori.ajax.reload();
        });
    }); 
  </script> 
@endpush  