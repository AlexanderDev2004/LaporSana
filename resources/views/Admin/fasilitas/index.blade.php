@extends('layouts.admin.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Fasilitas</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ route('admin.fasilitas.create') }}')" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Fasilitas
                </button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group-row">
                        <label class="col-1 control-label col-form-label">Filter:</label>
                        <div class="col-3">
                            <select class="form-control" id="ruangan_id" name="ruangan_id" required>
                                <option value="">- Semua -</option>
                                @foreach($ruangan as $item)
                                    <option value="{{ $item->ruangan_id }}">{{ $item->ruangan_nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Ruangan</small>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered" id="table_fasilitas">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode Fasilitas</th>
                        <th>Nama Fasilitas</th>
                        <th>Tingkat Urgensi</th>
                        <th>Ruangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
            });
        }

        var dataFasilitas;
        $(document).ready(function () {
            dataFasilitas = $('#table_fasilitas').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ route('admin.fasilitas.list') }}",
                    "dataType": "json",
                    "type": "GET",
                    "data": function (r) {
                        r.ruangan_id = $('#ruangan_id').val();
                    },
                    "error": function (xhr, error, thrown) {
                        console.log('Error pada DataTables:', error);
                        console.log('Response:', xhr.responseText);
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                    { data: 'fasilitas_kode', className: '', orderable: true, searchable: true },
                    { data: 'fasilitas_nama', className: '', orderable: true, searchable: true },
                    {
                        data: 'tingkat_urgensi',
                        className: '',
                        orderable: true,
                        searchable: true,
                        render: function (data, type, row) {
                            // Jika tipe render adalah untuk display atau filter
                            if (type === 'display' || type === 'filter') {
                                let badge, text;
                                switch (parseInt(data)) {
                                    case 1:
                                        badge = 'badge-danger';
                                        text = 'Sangat tidak penting';
                                        break;
                                    case 2:
                                        badge = 'badge-warning';
                                        text = 'Cukup tidak penting';
                                        break;
                                    case 3:
                                        badge = 'badge-secondary';
                                        text = 'Netral';
                                        break;
                                    case 4:
                                        badge = 'badge-info';
                                        text = 'Cukup penting';
                                        break;
                                    case 5:
                                        badge = 'badge-success';
                                        text = 'Sangat penting';
                                        break;
                                    default:
                                        badge = 'badge-secondary';
                                        text = 'Tidak diketahui';
                                }

                                return '<span class="badge ' + badge + '">' + data + ' - ' + text + '</span>';
                            }
                            // Untuk tipe sorting atau lainnya, kembalikan nilai numerik asli
                            return data;
                        }
                    },
                    { data: 'ruangan.ruangan_nama', className: '', orderable: false, searchable: false },
                    { data: 'aksi', className: '', orderable: false, searchable: false }
                ]
            });
            $('#ruangan_id').on('change', function () {
                dataFasilitas.ajax.reload();
            });
        });
    </script>
@endpush