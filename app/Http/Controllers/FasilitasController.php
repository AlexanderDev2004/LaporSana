<?php

namespace App\Http\Controllers;

use App\Models\LantaiModel;
use App\Models\FasilitasModel;
use App\Models\RuanganModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class FasilitasController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Fasilitas',
            'list'  => ['Home', 'Fasilitas']
        ];

        $active_menu = 'fasilitas';
        $ruangan = RuanganModel::all();

        return view('admin.fasilitas.index', compact('breadcrumb', 'active_menu', 'ruangan'));
    }

     public function list(Request $request)
    {
        $fasilitas = FasilitasModel::select('fasilitas_id', 'fasilitas_kode', 'fasilitas_nama', 'ruangan_id')
        ->with('ruangan');

        if ($request->ruangan_id) {
            $fasilitas->where('ruangan_id', $request->ruangan_id);
        }

        return DataTables::of($fasilitas)
            ->addIndexColumn()
            ->addColumn('aksi', function ($fasilitas) {
                // $btn = '<a href="' . url('/lantai/' . $lantai->lantai_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="' . url('/lantai/' . $lantai->lantai_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/lantai/' . $lantai->lantai_id) . '">'
                //     . csrf_field() . method_field('DELETE')
                //     . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah anda yakin menghapus data ini?\');">Hapus</button></form>';

                $btn = '<button onclick="modalAction(\''.route('admin.fasilitas.show', $fasilitas->fasilitas_id).'\')" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>';
                $btn .= '<button onclick="modalAction(\''.route('admin.fasilitas.edit', $fasilitas->fasilitas_id).'\')" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="modalAction(\''.route('admin.fasilitas.confirm', $fasilitas->fasilitas_id).'\')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function create()
    {
         $breadcrumb = (object) [
            'title' => 'Tambah Fasiltas',
            'list'  => ['Home', 'Fasiltas', 'Tambah']
        ];

        $active_menu = 'fasilitas';
        $ruangan = RuanganModel::select('ruangan_id', 'ruangan_nama')->get();
        
        return view('admin.fasilitas.create', compact('breadcrumb', 'active_menu'))
                    ->with('ruangan', $ruangan);
    }
  
          public function store(Request $request)
        {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'fasilitas_kode' => 'required',
                'fasilitas_nama' => 'required',
                'ruangan_id'    => 'required'
            ]);

            // If validation fails, return with errors
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            // Create new
            try {
                $fasilitas = new FasilitasModel();
                $fasilitas->fasilitas_kode = $request->fasilitas_kode;
                $fasilitas->fasilitas_nama = $request->fasilitas_nama;
                $fasilitas->ruangan_id = $request->ruangan_id;
                $fasilitas->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Data Fasilitas berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menyimpan data: ' . $e->getMessage()
                ]);
            }
        }
    public function edit(string $id) {
            $fasilitas = FasilitasModel::find(id: $id);
            $ruangan = RuanganModel::select('ruangan_id', 'ruangan_nama')->get();

            return view('admin.fasilitas.edit', ['fasilitas' => $fasilitas, 'ruangan' => $ruangan]); 
        }

      public function update(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'ruangan_id'    => 'required|exists:m_ruangan,ruangan_id',
                'fasilitas_kode' => 'required',
                'fasilitas_nama' => 'required|min:3|max:50',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $check = FasilitasModel::find($id);
            if ($check) {
                $check->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm(string $id) {
        $fasilitas = FasilitasModel::find($id);

        return view('admin.fasilitas.confirm', ['fasilitas' => $fasilitas]);
    }
    public function delete(Request $request, $id)
    {
        $fasilitas = FasilitasModel::find($id);
        if (!$fasilitas) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        try {
            $fasilitas->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data fasilitas$fasilitas berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function show(FasilitasModel $fasilitas)
    {
        $breadcrumb = (object) [
            'title' => 'Detail fasilitas',
            'list'  => ['Home', 'fasilitas', 'Detail']
        ];

        $active_menu = 'fasilitas';

        return view('admin.fasilitas.show', compact('breadcrumb', 'active_menu', 'fasilitas'));
    }
}
