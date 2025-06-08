@extends('layouts.sarpras.template')

@section('content')
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
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
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function modalAction(url = '') { 
    $('#myModal').load(url, function() { 
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
            url: "{{ route('sarpras.list.Laporan') }}", 
            dataType: "json", 
            type: "GET",
        }, 
        columns: [
            { 
                data: "DT_RowIndex", 
                className: "text-center", 
                width: "5%", 
                orderable: false, 
                searchable: false 
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
        responsive: true
    });

    // SweetAlert konfirmasi untuk Setujui
    $(document).on('click', '.btn-approve', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Laporan akan disetujui!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // SweetAlert konfirmasi untuk Tolak
    $(document).on('click', '.btn-reject', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Laporan akan ditolak!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script> 
@endpush
