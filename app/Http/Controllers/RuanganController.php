<?php

namespace App\Http\Controllers;

use App\Models\LantaiModel;
use App\Models\RuanganModel;
use App\Traits\ExcelExportTrait;
use App\Traits\ExcelImportTrait;
use App\Traits\JsonResponseTrait;
use App\Traits\PdfExportTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RuanganController extends Controller
{
    use JsonResponseTrait, ExcelExportTrait, ExcelImportTrait, PdfExportTrait;
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

        $active_menu = 'fasilitas';
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
                return $this->jsonValidationError($validator);
            }

            // Create new
            try {
                $ruangan = new RuanganModel();
                $ruangan->ruangan_kode = $request->ruangan_kode;
                $ruangan->ruangan_nama = $request->ruangan_nama;
                $ruangan->lantai_id = $request->lantai_id;
                $ruangan->save();

                return $this->jsonSuccess('Data Ruangan berhasil disimpan');
            } catch (\Exception $e) {
                return $this->jsonError('Gagal menyimpan data: ' . $e->getMessage());
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
                return $this->jsonError('Validasi gagal.', $validator->errors()->toArray());
            }

            $check = RuanganModel::find($id);
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
        $ruangan = RuanganModel::find($id);

        return view('admin.ruangan.confirm', ['ruangan' => $ruangan]);
    }
    public function delete(Request $request, $id)
    {
        $ruangan = RuanganModel::find($id);
        if (!$ruangan) {
            return $this->jsonError('Data tidak ditemukan');
        }
        try {
            $ruangan->delete();
            return $this->jsonSuccess('Data ruangan berhasil dihapus');
        } catch (\Exception $e) {
            return $this->jsonError('Gagal menghapus data: ' . $e->getMessage());
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

    public function import()
        {
                return view('admin.ruangan.import');
        }

        public function import_ajax(Request $request)
        {
            return $this->importExcel(
                $request,
                'file_ruangan',
                function ($value) {
                    return [
                        'lantai_id' => $value['A'],
                        'ruangan_kode' => $value['B'],
                        'ruangan_nama' => $value['C'],
                    ];
                },
                RuanganModel::class
            );
        }

        public function export_excel()
        {
            $ruangan = RuanganModel::select('lantai_id', 'ruangan_kode', 'ruangan_nama')
                ->orderBy('lantai_id')
                ->with('lantai')
                ->get();

            $headers = [
                'A' => 'No',
                'B' => 'Kode Ruangan',
                'C' => 'Nama Ruangan',
                'D' => 'Lantai'
            ];

            $data = [];
            $no = 1;
            foreach ($ruangan as $item) {
                $data[] = [
                    'A' => $no,
                    'B' => $item->ruangan_kode,
                    'C' => $item->ruangan_nama,
                    'D' => $item->lantai->lantai_nama
                ];
                $no++;
            }

            $spreadsheet = $this->createSpreadsheet($headers, $data, 'Data Ruangan');
            $filename = 'Data Ruangan ' . date('Y-m-d H:i:s') . '.xlsx';
            $this->exportSpreadsheet($spreadsheet, $filename);
        } // end function export_excel

        public function export_pdf()
        {
            $ruangan = RuanganModel::select('lantai_id', 'ruangan_kode', 'ruangan_nama')
                ->orderBy('lantai_id')
                ->with('lantai')
                ->get();

            return $this->generatePdf(
                'admin.ruangan.export_pdf',
                ['ruangan' => $ruangan],
                'Data Ruangan ' . date('Y-m-d H:i:s') . '.pdf',
                'portrait'
            );
        }
}
