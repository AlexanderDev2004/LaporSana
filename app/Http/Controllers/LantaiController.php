<?php

namespace App\Http\Controllers;

use App\Models\LantaiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LantaiController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen lantai',
            'list'  => ['Home', 'lantai']
        ];

        $active_menu = 'lantai';
        $lantai = LantaiModel::all();

        return view('admin.lantai.index', compact('breadcrumb', 'active_menu', 'lantai'));
    }

     public function list(Request $request)
    {
        $lantai = LantaiModel::select('lantai_id', 'lantai_kode', 'lantai_nama');

        if ($request->lantai_id) {
            $lantai->where('lantai_id', $request->lantai_id);
        }

        return DataTables::of($lantai)
            ->addIndexColumn()
            ->addColumn('aksi', function ($lantai) {
                // $btn = '<a href="' . url('/lantai/' . $lantai->lantai_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="' . url('/lantai/' . $lantai->lantai_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/lantai/' . $lantai->lantai_id) . '">'
                //     . csrf_field() . method_field('DELETE')
                //     . '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah anda yakin menghapus data ini?\');">Hapus</button></form>';

                $btn = '<button onclick="modalAction(\''.route('admin.lantai.show', $lantai->lantai_id).'\')" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></button>';
                $btn .= '<button onclick="modalAction(\''.route('admin.lantai.edit', $lantai->lantai_id).'\')" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>';
                $btn .= '<button onclick="modalAction(\''.route('admin.lantai.confirm', $lantai->lantai_id).'\')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }


    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Lantai',
            'list'  => ['Home', 'lantai', 'Tambah']
        ];

        $active_menu = 'lantai';
        return view('admin.lantai.create', compact('breadcrumb', 'active_menu'));
    }

        public function store(Request $request)
        {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'lantai_kode' => 'required|string|max:5',
                'lantai_nama' => 'required|string|min:3|max:50'
            ]);

            // If validation fails, return with errors
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            // Create new lantai
            try {
                $lantai = new LantaiModel();
                $lantai->lantai_kode = $request->lantai_kode;
                $lantai->lantai_nama = $request->lantai_nama;
                $lantai->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Data lantai berhasil disimpan'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menyimpan data: ' . $e->getMessage()
                ]);
            }
        }
    public function edit(string $id) {
            $lantai = LantaiModel::find($id);

            return view('admin.lantai.edit', ['lantai' => $lantai]);
        }

      public function update(Request $request, $id) {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'lantai_kode' => 'required|string|max:5',
                'lantai_nama' => 'required|string|min:3|max:50'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status'   => false,
                    'message'  => 'Validasi gagal.',
                    'msgField' => $validator->errors()
                ]);
            }

            $check = LantaiModel::find($id);
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
        $lantai = LantaiModel::find($id);

        return view('admin.lantai.confirm', ['lantai' => $lantai]);
    }
    public function delete(Request $request, $id)
    {
        $lantai = LantaiModel::find($id);
        if (!$lantai) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }
        try {
            $lantai->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data lantai berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function show(LantaiModel $lantai)
    {
        $breadcrumb = (object) [
            'title' => 'Detail Lantai',
            'list'  => ['Home', 'lantai', 'Detail']
        ];

        $active_menu = 'lantai';

        return view('admin.lantai.show', compact('breadcrumb', 'active_menu', 'lantai'));
    }
}
