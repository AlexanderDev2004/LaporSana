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



        return view('admin.validasi_laporan.index', compact('breadcrumb', 'active_menu', 'user', 'status', 'details'));
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
                $btn = '<button onclick="modalAction(\'' . route('admin.validasi_laporan.show', $laporan->laporan_id) . '\')" class="btn btn-info btn-sm mr-1"><i class="fas fa-eye"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }



    public function show($laporan_id)
    {
        $laporan = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status', 'user'])
            ->where('laporan_id', $laporan_id)
            ->firstOrFail();

        return view('admin.validasi_laporan.show', compact('laporan'));
    }


    public function verify(Request $request, $laporan_id)
    {
        $laporan = LaporanModel::findOrFail($laporan_id);

        if ($request->verifikasi == 'tolak') {
            $laporan->status_id = 2; // Status ditolak
            $laporan->save();

            // Notify user of rejection (optional)
            // $laporan->user->notify(new LaporanStatusUpdated($laporan, 'Ditolak'));

            return response()->json(['status' => true, 'message' => 'Laporan berhasil ditolak']);
        } elseif ($request->verifikasi == 'setuju') {
            $laporan->status_id = 5; // Status disetujui
            $laporan->save();

            return response()->json(['status' => true, 'message' => 'Laporan berhasil disetujui']);
        }

        return response()->json(['status' => false, 'message' => 'Aksi tidak valid']);
    }
}
