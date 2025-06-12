@extends('layouts.pelapor.template')

@section('content')
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button type="button" onclick="modalAction('{{ route('pelapor.create') }}')" class="btn btn-primary">Tambah Laporan</button>
        </div>
    </div>
    <div class="card-body"> 
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <table class="table table-bordered table-striped table-hover table-sm" id="table_laporan"> 
            <thead> 
                <tr>
                    <th>No</th>
                    <th>Fasilitas</th>
                    <th>Ruangan</th>
                    <th>Lantai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr> 
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
@endsection

@push('css')
<style>
    #table_laporan {
        border: 1px solid #ffffff;
    }
    #table_laporan th {
        border-color: #ffffff;
    }
    .modal {
        z-index: 1050;
    }
    .modal-backdrop {
        z-index: 1040;
    }
    .content-wrapper {
        z-index: 1;
    }
</style>
@endpush

@push('js') 
<script>
    function modalAction(url = ''){ 
        $('#myModal').load(url, function(){ 
            $(this).modal('show'); 
        }); 
    }

    var dataLaporan; 
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }); 

        dataLaporan = $('#table_laporan').DataTable({ 
            processing: true, 
            serverSide: true,
            ajax: { 
                url: "{{ route('pelapor.list') }}", 
                dataType: "json", 
                type: "POST",
            }, 
            columns: [
                { 
                    data: "DT_RowIndex", 
                    className: "text-center", 
                    width: "5%", 
                    orderable: false, 
                    searchable: true 
                },{ 
                    data: "details.0.fasilitas.fasilitas_nama",
                    width: "15%", 
                    defaultContent: "-"
                },{ 
                    data: "details.0.fasilitas.ruangan.ruangan_nama",
                    width: "15%",  
                    defaultContent: "-" 
                },
                { 
                    data: "details.0.fasilitas.ruangan.lantai.lantai_nama",
                    width: "15%",  
                    defaultContent: "-" 
                },
                { 
                    data: "status.status_nama",
                    width: "15%",  
                    defaultContent: "-" 
                },
                { 
                    data: "aksi",
                    className: "text-center", 
                    width: "15%", 
                    orderable: false, 
                    searchable: false 
                }
            ],
            responsive: true,
            columnDefs: [{
                targets: -1,
                data: null,
                defaultContent: '<button class="btn btn-info btn-sm btn-detail"><i class="fas fa-eye"></i> Detail</button>'
            }]
        });
        $('#table-laporan_filter input').unbind().bind().on('keyup', function(e){ 
        if(e.keyCode == 13){ // enter key 
            dataLaporan.search(this.value).draw(); 
        } 
    });
    }); 
</script> 
@endpush