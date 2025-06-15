<!DOCTYPE html>
<html>
    <head>
        <title>Data Tugas</title>
    </head>
    <body>
        <h1>Data Tugas</h1>
        <table border="1" cellpadding="2" cellspacing="0">   
            <tr>
                <td>ID</td>
                <td>Pengguna</td>
                <td>Status</td>
                <td>Tugas Jenis</td>
                <td>Tugas Mulai</td>
                <td>Tugas Selesai</td>
                <td>Aksi</td>
            </tr>
            @foreach ($data as $d)
             <tr>
                    <td>{{ $d->tugas_id }}</td>
                    <td>{{ $d->user->nama ?? '-' }}</td>
                    <td>{{ $d->status->status_nama ?? '-' }}</td>
                    <td>{{ $d->tugas_jenis }}</td>
                    <td>{{ $d->tugas_mulai }}</td>
                    <td>{{ $d->tugas_selesai }}</td>
                    <td>
                        <a href="{{ url('teknisi/' . $d->tugas_id . '/edit') }}">Ubah</a> 
                        <a href="{{ url('teknisi/' . $d->tugas_id . '/hapus') }}">Hapus</a>
                    </td>
                </tr>
            @endforeach
        </table>
    </body>
</html>