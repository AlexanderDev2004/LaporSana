<?php

namespace App\Http\Controllers;

use App\Models\{LaporanModel, TugasModel, TugasDetailModel, UserModel};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log};
use Yajra\DataTables\Facades\DataTables;

class ValidlaporAController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Selamat Datang',
            'list'  => ['Home', 'Validasi Laporan']
        ];

        $active_menu = 'validasi laporan';
        return view('admin.validasi_laporan.index', compact('breadcrumb', 'active_menu'));
    }

    public function list(Request $request)
    {
        $laporans = LaporanModel::with(['user', 'status', 'details.fasilitas.ruangan.lantai'])
            ->select('m_laporan.*')
            ->where('status_id', config('constants.status.pending'));

        return DataTables::of($laporans)
            ->addColumn('pelapor', fn($laporan) => $laporan->user?->name ?? 'N/A')
            ->addColumn('tanggal', fn($laporan) => $laporan->created_at ? $laporan->created_at->format('d/m/Y H:i') : '-')
            ->addColumn('status', fn($laporan) => $laporan->status?->status_nama ?? 'N/A')
            ->addColumn('aksi', function ($laporan) {
                $detailUrl = route('admin.validasi_laporan.show', $laporan->laporan_id);
                $btn = '<button onclick="modalAction(\'' . $detailUrl . '\')" class="btn btn-info btn-sm me-2">Detail</button>';
                if ($laporan->status_id == config('constants.status.pending')) {
                    $btn .= '<button onclick="setujuAction(' . $laporan->laporan_id . ')" class="btn btn-success btn-sm me-2">Setujui</button>';
                    $btn .= '<button onclick="tolakAction(' . $laporan->laporan_id . ')" class="btn btn-danger btn-sm">Tolak</button>';
                }
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function setuju(Request $request, $laporan_id)
    {
        // Tambahkan pengecekan role admin
        if (Auth::user()->roles_id != 1) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya admin yang dapat melakukan aksi ini.'], 403);
        }

        $request->validate([
            'laporan_id' => 'required|numeric'
        ]);

        if (!is_numeric($laporan_id)) {
            return response()->json(['success' => false, 'message' => 'ID laporan tidak valid.']);
        }

        DB::beginTransaction();
        try {
            $laporan = LaporanModel::with('details')->findOrFail($laporan_id);

            if ($laporan->status_id != config('constants.status.pending')) {
                throw new \Exception('Laporan tidak dalam status pending');
            }

            if ($laporan->details->isEmpty()) {
                throw new \Exception('Laporan tidak memiliki detail perbaikan');
            }

            $laporan->status_id = config('constants.status.diproses');
            $laporan->updated_by = Auth::id();
            $laporan->save();

            $saranaUser = UserModel::where('roles_id', 5)
                ->withCount(['tugas as tugas_aktif_count' => function ($q) {
                    $q->where('status_id', config('constants.status.diproses'));
                }])
                ->orderBy('tugas_aktif_count')
                ->first();

            if (!$saranaUser) {
                throw new \Exception('User Sarpras tidak ditemukan');
            }

            $tugas = TugasModel::create([
                'user_id' => $saranaUser->user_id,
                'laporan_id' => $laporan->laporan_id,
                'status_id' => config('constants.status.diproses'),
                'tugas_jenis' => 'perbaikan',
                'tugas_mulai' => now(),
                'tugas_selesai' => now()->addDays(7),
            ]);

            foreach ($laporan->details as $detail) {
                TugasDetailModel::create([
                    'tugas_id' => $tugas->tugas_id,
                    'fasilitas_id' => $detail->fasilitas_id,
                    'tugas_image' => $detail->foto_bukti,
                    'deskripsi' => $detail->deskripsi,
                    'status_id' => config('constants.status.diproses'),
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Laporan disetujui dan ditugaskan ke Sarana Prasarana.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in setuju: ' . $e->getMessage(), ['laporan_id' => $laporan_id]);
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function tolak(Request $request, $laporan_id)
    {
        // Tambahkan pengecekan role admin
        if (Auth::user()->roles_id != 1) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya admin yang dapat melakukan aksi ini.'], 403);
        }

        $request->validate([
            'laporan_id' => 'required|numeric'
        ]);

        if (!is_numeric($laporan_id)) {
            return response()->json(['success' => false, 'message' => 'ID laporan tidak valid.']);
        }

        DB::beginTransaction();
        try {
            $laporan = LaporanModel::findOrFail($laporan_id);

            if ($laporan->status_id != config('constants.status.pending')) {
                throw new \Exception('Laporan tidak dalam status pending');
            }

            $laporan->status_id = config('constants.status.ditolak');
            $laporan->updated_by = Auth::id();
            $laporan->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Laporan telah ditolak.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in tolak: ' . $e->getMessage(), ['laporan_id' => $laporan_id]);
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function show($laporan_id)
    {
        $laporan = LaporanModel::with(['details.fasilitas.ruangan.lantai'])
            ->when(Auth::user()->roles_id == 3, function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('laporan_id', $laporan_id)
            ->firstOrFail();

        return view('admin.validasi_laporan.show', compact('laporan'));
    }
}
