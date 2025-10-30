<?php

namespace App\Http\Controllers;

use App\Models\FasilitasModel;
use App\Models\RuanganModel;
use App\Traits\ExcelExportTrait;
use App\Traits\ExcelImportTrait;
use App\Traits\JsonResponseTrait;
use App\Traits\PdfExportTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class FasilitasController extends Controller
{
    use ExcelExportTrait, ExcelImportTrait, JsonResponseTrait, PdfExportTrait;

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Manajemen Fasilitas',
            'list' => ['Home', 'Fasilitas'],
        ];

        $active_menu = 'fasilitas';
        $ruangan = RuanganModel::all();

        return view('admin.fasilitas.index', compact('breadcrumb', 'active_menu', 'ruangan'));
    }

    public function list(Request $request)
    {
        $fasilitas = FasilitasModel::select('fasilitas_id', 'fasilitas_kode', 'fasilitas_nama', 'tingkat_urgensi', 'ruangan_id')
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
            'title' => 'Tambah Fasilitas',
            'list' => ['Home', 'Fasilitas', 'Tambah'],
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
            'tingkat_urgensi' => 'required|in:1,2,3,4,5',
            'ruangan_id' => 'required',
        ]);

        // If validation fails, return with errors
        if ($validator->fails()) {
            return $this->jsonValidationError($validator);
        }

        // Create new
        try {
            $fasilitas = new FasilitasModel;
            $fasilitas->fasilitas_kode = $request->fasilitas_kode;
            $fasilitas->fasilitas_nama = $request->fasilitas_nama;
            $fasilitas->tingkat_urgensi = $request->tingkat_urgensi;
            $fasilitas->ruangan_id = $request->ruangan_id;
            $fasilitas->save();

            return $this->jsonSuccess('Data Fasilitas berhasil disimpan');
        } catch (\Exception $e) {
            return $this->jsonError('Gagal menyimpan data: '.$e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $fasilitas = FasilitasModel::find(id: $id);
        $ruangan = RuanganModel::select('ruangan_id', 'ruangan_nama')->get();

        return view('admin.fasilitas.edit', ['fasilitas' => $fasilitas, 'ruangan' => $ruangan]);
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'ruangan_id' => 'required|exists:m_ruangan,ruangan_id',
                'fasilitas_kode' => 'required',
                'fasilitas_nama' => 'required|min:3|max:50',
                'tingkat_urgensi' => 'required|in:1,2,3,4,5', // Validasi tingkat urgensi
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->jsonError('Validasi gagal.', $validator->errors()->toArray());
            }

            $check = FasilitasModel::find($id);
            if ($check) {
                $check->update($request->all());

                return $this->jsonSuccess('Data berhasil diupdate');
            } else {
                return $this->jsonError('Data tidak ditemukan');
            }
        }

        return redirect('/');
    }

    public function confirm(string $id)
    {
        $fasilitas = FasilitasModel::find($id);

        return view('admin.fasilitas.confirm', ['fasilitas' => $fasilitas]);
    }

    public function delete(Request $request, $id)
    {
        $fasilitas = FasilitasModel::find($id);
        if (! $fasilitas) {
            return $this->jsonError('Data tidak ditemukan');
        }
        try {
            $fasilitas->delete();

            return $this->jsonSuccess('Data fasilitas berhasil dihapus');
        } catch (\Exception $e) {
            return $this->jsonError('Gagal menghapus data: '.$e->getMessage());
        }
    }

    public function show(FasilitasModel $fasilitas)
    {
        $breadcrumb = (object) [
            'title' => 'Detail fasilitas',
            'list' => ['Home', 'fasilitas', 'Detail'],
        ];

        $active_menu = 'fasilitas';

        return view('admin.fasilitas.show', compact('breadcrumb', 'active_menu', 'fasilitas'));
    }

    public function import()
    {
        return view('admin.fasilitas.import');
    }

    public function import_ajax(Request $request)
    {
        return $this->importExcel(
            $request,
            'file_fasilitas',
            function ($value) {
                return [
                    'ruangan_id' => $value['A'],
                    'fasilitas_kode' => $value['B'],
                    'fasilitas_nama' => $value['C'],
                    'tingkat_urgensi' => $value['D'],
                ];
            },
            FasilitasModel::class
        );
    }

    public function export_excel()
    {
        $fasilitas = FasilitasModel::select('ruangan_id', 'fasilitas_kode', 'fasilitas_nama', 'tingkat_urgensi')
            ->orderBy('fasilitas_nama')
            ->with('ruangan')
            ->get();

        $headers = [
            'A' => 'No',
            'B' => 'Fasilitas Kode',
            'C' => 'Fasilitas Nama',
            'D' => 'Nama Ruangan',
            'E' => 'Tingkat Urgensi',
        ];

        $data = [];
        $no = 1;
        foreach ($fasilitas as $item) {
            $data[] = [
                'A' => $no,
                'B' => $item->fasilitas_kode,
                'C' => $item->fasilitas_nama,
                'D' => $item->ruangan->ruangan_nama,
                'E' => $item->tingkat_urgensi,
            ];
            $no++;
        }

        $spreadsheet = $this->createSpreadsheet($headers, $data, 'Data Fasilitas');
        $filename = 'Data Fasilitas '.date('Y-m-d H:i:s').'.xlsx';
        $this->exportSpreadsheet($spreadsheet, $filename);
    } // end function export_excel

    public function export_pdf()
    {
        $fasilitas = FasilitasModel::select('ruangan_id', 'fasilitas_kode', 'fasilitas_nama', 'tingkat_urgensi')
            ->orderBy('fasilitas_nama')
            ->with('ruangan')
            ->get();

        return $this->generatePdf(
            'admin.fasilitas.export_pdf',
            ['fasilitas' => $fasilitas],
            'Data Fasilitas '.date('Y-m-d H:i:s').'.pdf',
            'portrait'
        );
    }
}
