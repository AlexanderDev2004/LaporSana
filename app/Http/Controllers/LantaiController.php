<?php

namespace App\Http\Controllers;

use App\Models\LantaiModel;
use App\Traits\ExcelExportTrait;
use App\Traits\ExcelImportTrait;
use App\Traits\JsonResponseTrait;
use App\Traits\PdfExportTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LantaiController extends Controller
{
    use JsonResponseTrait, ExcelExportTrait, ExcelImportTrait, PdfExportTrait;
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
                return $this->jsonValidationError($validator);
            }

            // Create new lantai
            try {
                $lantai = new LantaiModel();
                $lantai->lantai_kode = $request->lantai_kode;
                $lantai->lantai_nama = $request->lantai_nama;
                $lantai->save();

                return $this->jsonSuccess('Data lantai berhasil disimpan');
            } catch (\Exception $e) {
                return $this->jsonError('Gagal menyimpan data: ' . $e->getMessage());
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
                return $this->jsonError('Validasi gagal.', $validator->errors()->toArray());
            }

            $check = LantaiModel::find($id);
            if ($check) {
                $check->update($request->all());
                return $this->jsonSuccess('Data berhasil diupdate');
            } else {
                return $this->jsonError('Data tidak ditemukan');
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
            return $this->jsonError('Data tidak ditemukan');
        }
        try {
            $lantai->delete();
            return $this->jsonSuccess('Data lantai berhasil dihapus');
        } catch (\Exception $e) {
            return $this->jsonError('Gagal menghapus data: ' . $e->getMessage());
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

   public function import()
        {
                return view('admin.lantai.import');
        }

     public function import_ajax(Request $request)
        {
            return $this->importExcel(
                $request,
                'file_lantai',
                function ($value) {
                    return [
                        'lantai_kode' => $value['A'],
                        'lantai_nama' => $value['B'],
                    ];
                },
                LantaiModel::class
            );
        }

        public function export_excel()
        {
            $lantai = LantaiModel::select('lantai_kode', 'lantai_nama')
                ->orderBy('lantai_nama')
                ->get();

            $headers = [
                'A' => 'No',
                'B' => 'Lantai Kode',
                'C' => 'Lantai Nama'
            ];

            $data = [];
            $no = 1;
            foreach ($lantai as $item) {
                $data[] = [
                    'A' => $no,
                    'B' => $item->lantai_kode,
                    'C' => $item->lantai_nama
                ];
                $no++;
            }

            $spreadsheet = $this->createSpreadsheet($headers, $data, 'Data Lantai');
            $filename = 'Data Lantai ' . date('Y-m-d H:i:s') . '.xlsx';
            $this->exportSpreadsheet($spreadsheet, $filename);
        } // end function export_excel

        public function export_pdf()
        {
            $lantai = LantaiModel::select('lantai_kode', 'lantai_nama')
                ->orderBy('lantai_nama')
                ->get();

            return $this->generatePdf(
                'admin.lantai.export_pdf',
                ['lantai' => $lantai],
                'Data Lantai ' . date('Y-m-d H:i:s') . '.pdf',
                'portrait'
            );
        }
}
