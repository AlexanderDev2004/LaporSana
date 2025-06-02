<?php

namespace App\Http\Controllers;

use App\Models\LantaiModel;
use App\Models\RuanganModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RuanganController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Ruangan',
            'list'  => ['Home', 'Ruangan']
        ];

        $active_menu = 'ruangan';
        $lantai = LantaiModel::all();

        return view('admin.ruangan.index', compact('breadcrumb', 'active_menu', 'lantai'));
    }

     public function list(Request $request)
    {
        $ruangan = RuanganModel::select('ruangan_id', 'ruangan_kode', 'ruangan_nama', 'lantai_id')
        ->with('lantai');

        if ($request->lantai_id) {
            $ruangan->where('lantai_id', $request->lantai_id);
        }

        return DataTables::of($ruangan)
            ->addIndexColumn()
            ->addColumn('aksi', function ($ruangan) {
                // $btn = '<a href="' . url('/lantai/' . $lantai->lantai_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="' . url('/lantai/' . $lantai->lantai_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/lantai/' . $lantai->lantai_id) . '">'
                //     . csrf_field() . method_field('DELETE')
                //     . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah anda yakin menghapus data ini?\');">Hapus</button></form>';

                $btn = '<button onclick="modalAction(\''.route('admin.ruangan.show', $ruangan->ruangan_id).'\')" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>';
                $btn .= '<button onclick="modalAction(\''.route('admin.ruangan.edit', $ruangan->ruangan_id).'\')" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="modalAction(\''.route('admin.ruangan.confirm', $ruangan->ruangan_id).'\')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function create()
    {
         $breadcrumb = (object) [
            'title' => 'Tambah Ruangan',
            'list'  => ['Home', 'Ruangan', 'Tambah']
        ];

        $active_menu = 'roles';
        $lantai = LantaiModel::select('lantai_id', 'lantai_nama')->get();
        
        return view('admin.ruangan.create', compact('breadcrumb', 'active_menu'))
                    ->with('lantai', $lantai);
    }
  
          public function store(Request $request)
        {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'ruangan_kode' => 'required',
                'ruangan_nama' => 'required',
                'lantai_id'    => 'required'
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
                $ruangan = new RuanganModel();
                $ruangan->ruangan_kode = $request->ruangan_kode;
                $ruangan->ruangan_nama = $request->ruangan_nama;
                $ruangan->lantai_id = $request->lantai_id;
                $ruangan->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Data Ruangan berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menyimpan data: ' . $e->getMessage()
                ]);
            }
        }
    public function edit(string $id) {
            $ruangan = RuanganModel::find(id: $id);
            $lantai = LantaiModel::select('lantai_id', 'lantai_nama')->get();

            return view('admin.ruangan.edit', ['ruangan' => $ruangan, 'lantai' => $lantai]); 
        }

      public function update(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'lantai_id'    => 'required|exists:m_lantai,lantai_id',
                'ruangan_kode' => 'required',
                'ruangan_nama' => 'required|min:3|max:50',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $check = RuanganModel::find($id);
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
        $ruangan = RuanganModel::find($id);

        return view('admin.ruangan.confirm', ['ruangan' => $ruangan]);
    }
    public function delete(Request $request, $id)
    {
        $ruangan = RuanganModel::find($id);
        if (!$ruangan) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        try {
            $ruangan->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data ruangan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function show(RuanganModel $ruangan)
    {
        $breadcrumb = (object) [
            'title' => 'Detail Ruangan',
            'list'  => ['Home', 'Ruangan', 'Detail']
        ];

        $active_menu = 'ruangan';

        return view('admin.ruangan.show', compact('breadcrumb', 'active_menu', 'ruangan'));
    }
}
