<?php

namespace App\Http\Controllers;

use App\Models\LaporanDetail;
use App\Models\LaporanModel;
use App\Models\TugasModel;
use App\Models\TugasDetail;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LaporanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Laporan',
            'list'  => ['Home', 'Laporan']
        ];

        $active_menu = 'laporan';

        return view('admin.laporan.index', compact('breadcrumb', 'active_menu'));   
    }

    public function list(Request $request)
    {
        $laporan = LaporanModel::with(['user', 'status', 'details.fasilitas'])
            ->where('status_id', config('constants.status_menunggu'));

        return DataTables::of($laporan)
            ->addIndexColumn()
            ->addColumn('nama_user', function ($item) {
                return $item->user->nama ?? '-';
            })
            ->addColumn('nama_fasilitas', function ($item) {
                $detail = $item->details->first();
                return $detail ? ($detail->fasilitas->fasilitas_nama ?? '-') : '-';
            })
            ->addColumn('status', function ($item) {
                return $item->status->status_nama ?? '-';
            })
            ->addColumn('tanggal_lapor', function ($item) {
                return $item->tanggal_lapor ? $item->tanggal_lapor->format('d-m-Y H:i') : '-';
            })
            ->addColumn('aksi', function ($item) {
                $btn = '<button onclick="modalAction(\'' . route('admin.laporan.show', $item->laporan_id) . '\')" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Detail</button> ';
                $btn .= '<button onclick="verifyLaporan(\'' . $item->laporan_id . '\', \'setujui\')" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Setujui</button> ';
                $btn .= '<button onclick="verifyLaporan(\'' . $item->laporan_id . '\', \'tolak\')" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Tolak</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show($laporan_id)
    {
        $laporan = LaporanModel::with(['details.fasilitas', 'user', 'status'])->findOrFail($laporan_id);
        return view('admin.laporan.show', compact('laporan'));
    }

    public function verify(Request $request, $laporan_id)
    {
        $laporan = LaporanModel::findOrFail($laporan_id);

        if ($request->verifikasi == 'tolak') {
            $laporan->status_id = config('constants.status_ditolak');
            $laporan->save();

            // Notify user of rejection (optional)
            // $laporan->user->notify(new LaporanStatusUpdated($laporan, 'Ditolak'));

            return response()->json(['status' => true, 'message' => 'Laporan ditolak']);
        } elseif ($request->verifikasi == 'setujui') {
            $laporan->status_id = config('constants.status_diproses');
            $laporan->save();

            // Assign to Sarana Prasarana (role_id = 2 for example)
            $saranaUser = UserModel::where('role_id', 2)->first();
            if ($saranaUser) {
                $tugas = TugasModel::create([
                    'user_id' => $saranaUser->user_id,
                    'status_id' => config('constants.status_diproses'),
                    'tugas_jenis' => 'perbaikan',
                    'tugas_mulai' => now(),
                    'tugas_selesai' => now()->addDays(7),
                ]);

                // Copy details from laporan to tugas_detail
                foreach ($laporan->details as $detail) {
                    TugasDetail::create([
                        'tugas_id' => $tugas->tugas_id,
                        'fasilitas_id' => $detail->fasilitas_id,
                        'tugas_image' => $detail->foto_bukti,
                        'deskripsi' => $detail->deskripsi,
                    ]);
                }

                // Notify Sarana Prasarana user (optional)
                // $saranaUser->notify(new TugasAssigned($tugas));
            } else {
                return response()->json(['status' => false, 'message' => 'Tidak ada user Sarana Prasarana ditemukan']);
            }

            // Notify user of approval (optional)
            // $laporan->user->notify(new LaporanStatusUpdated($laporan, 'Diproses'));

            return response()->json(['status' => true, 'message' => 'Laporan disetujui dan diteruskan ke Sarana Prasarana']);
        }

        return response()->json(['status' => false, 'message' => 'Aksi tidak valid']);
    }
}
