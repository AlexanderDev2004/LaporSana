@extends('layouts.teknisi.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Tugas</h3>
        </div>

        <div class="card-body">

            {{-- Filter Status --}}
            <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label for="filter_status">Filter Status</label>
                        <select name="filter_status" class="form-control form-control-sm filter_status">
                            <option value="">- Semua -</option>
                            @foreach ($status as $s)
                                <option value="{{ $s->status_id }}">{{ $s->status_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Alert --}}
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Tabel Tugas --}}
            <table id="table_tugas" class="table table-bordered table-striped table-sm table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Teknisi</th>
                        <th>Status</th>
                        <th>Jenis Tugas</th>
                        <th>Tanggal Pengerjaan</th>
                        <th>Tanggal Selesai</th>
                        <th>Feedback</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection
@push('css')
@endpush
@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }
        var tableTugas;

        $(document).ready(function() {
            tableTugas = $('#table_tugas').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('teknisi.riwayat.list') }}",
                    "dataType": "json",
                    type: "GET",
                    data: function(d) {
                        d.filter_status = $('.filter_status').val();
                    }
                },
                columns: [{
                        data: null,
                        className: "text-center",
                        width: "5%",
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: "user.name",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "status.status_nama",
                        className: "",
                        render: function(data, type, row) {
                            let badgeClass = 'secondary';
                            switch (data.toLowerCase()) {
                                case 'pending':
                                    badgeClass = 'warning';
                                    break;
                                case 'dalam proses':
                                    badgeClass = 'primary';
                                    break;
                                case 'selesai':
                                    badgeClass = 'success';
                                    break;
                                case 'dibatalkan':
                                    badgeClass = 'danger';
                                    break;
                            }
                            return <span class="badge badge-${badgeClass}">${data}</span>;
                        }
                    },
                    {
                        data: "tugas_jenis",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "tugas_mulai",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "tugas_selesai",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#table_tugas_filter input').unbind().bind().on('keyup', function(e) {
                if (e.keyCode == 13) {
                    tableTugas.search(this.value).draw();
                }
            });

            $('.filter_status').change(function() {
                tableTugas.draw();
            });
        });
    </script>
@endpush
