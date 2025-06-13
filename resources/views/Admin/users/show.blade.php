@empty($user)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12 text-center">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" class="img-circle elevation-2" width="100" 
                                height="100" alt="User Avatar">
                        @else
                            <img src="{{ asset('LaporSana/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" 
                                width="100" height="100" alt="Default Avatar">
                        @endif
                    </div>
                </div>
                <table class="table table-sm table-bordered table-striped">
                    <tr>
                        <th class="text-right col-3">ID User :</th>
                        <td class="col-9">{{ $user->user_id }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Username :</th>
                        <td class="col-9">{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Nama Lengkap :</th>
                        <td class="col-9">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Role :</th>
                        <td class="col-9">{{ $user->role->roles_nama }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">NIM :</th>
                        <td class="col-9">{{ $user->NIM ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">NIP :</th>
                        <td class="col-9">{{ $user->NIP ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Tanggal Dibuat :</th>
                        <td class="col-9">{{ $user->created_at ? $user->created_at->format('d F Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-right col-3">Terakhir Diupdate :</th>
                        <td class="col-9">{{ $user->updated_at ? $user->updated_at->format('d F Y H:i') : '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" onclick="modalAction('{{ route('admin.users.edit', $user) }}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
            </div>
        </div>
    </div>
@endempty