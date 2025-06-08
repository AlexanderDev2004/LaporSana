<?php

namespace App\Http\Controllers;

use App\Models\FasilitasModel;
use App\Models\LaporanDetail;
use App\Models\LaporanModel;
use App\Models\StatusModel;
use App\Models\TugasModel;
use App\Models\TugasDetail;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class LaporanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Laporan',
            'list'  => ['Home', 'Laporan']
        ];

        $active_menu = 'laporan';
        $user = UserModel::all();
        $status = StatusModel::all();
        $details = LaporanDetail::all();
      


        return view('admin.laporan.index', compact('breadcrumb', 'active_menu', 'user', 'status', 'details'));
    }

    public function list(Request $request)
    {
        $laporan = LaporanModel::select('laporan_id', 'user_id', 'status_id', 'tanggal_lapor', 'jumlah_pelapor')
            ->with('user', 'status');
        if ($request->user_id) {
            $laporan->where('user_id', $request->user_id);
        }
        if ($request->status_id) {
            $laporan->where('status_id', $request->status_id);
        }

        return DataTables::of($laporan)
            ->addIndexColumn()
            ->addColumn('aksi', function ($laporan) {
                $btn = '<button onclick="modalAction(\'' . route('admin.laporan.show', $laporan->laporan_id) . '\')" class="btn btn-info btn-sm mr-1"><i class="fas fa-eye"></i></button>';

                // Only show edit and delete buttons if appropriate
                if ($laporan->status_id == 1) { // Status Menunggu
                    $btn .= '<button onclick="modalAction(\'' . route('admin.laporan.edit', $laporan->laporan_id) . '\')" class="btn btn-warning btn-sm mr-1"><i class="fas fa-edit"></i></button>';
                    $btn .= '<button onclick="modalAction(\'' . route('admin.laporan.confirm', $laporan->laporan_id) . '\')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

   
public function show($laporan_id)
{
    $laporan = LaporanModel::with(['details.fasilitas.ruangan', 'user', 'status'])->find($laporan_id);
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
