<?php

namespace App\Http\Controllers;

use App\Models\LantaiModel;
use App\Models\RuanganModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
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

    public function import()
        {
                return view('admin.ruangan.import');
        }

        public function import_ajax(Request $request)
        {
                if ($request->ajax() || $request->wantsJson()) {
                        $rules = [
                                // validasi file harus xls atau xlsx, max 1MB
                                'file_ruangan' => ['required', 'mimes:xlsx', 'max:1024']
                        ];

                        $validator = Validator::make($request->all(), $rules);

                        if ($validator->fails()) {
                                return response()->json([
                                        'status' => false,
                                        'message' => 'Validasi Gagal',
                                        'msgField' => $validator->errors()
                                ]);
                        }

                        $file = $request->file('file_ruangan'); // ambil file dari request

                        $reader = IOFactory::createReader('Xlsx'); // load reader file excel
                        $reader->setReadDataOnly(true); // hanya membaca data
                        $spreadsheet = $reader->load($file->getRealPath()); // load file excel
                        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

                        $data = $sheet->toArray(null, false, true, true); // ambil data excel

                        $insert = [];

                        if (count($data) > 1) { // jika data lebih dari 1 baris
                                foreach ($data as $baris => $value) {
                                        if ($baris > 1) { // baris ke 1 adalah header, maka lewati
                                                $insert[] = [
                                                        'lantai_id'      => $value['A'],
                                                        'ruangan_kode'   => $value['B'],
                                                        'ruangan_nama'   => $value['C'],
                                                        'created_at'     => now(),
                                                ];
                                        }
                                }

                                if (count($insert) > 0) {
                                        // insert data ke database, jika data sudah ada, maka diabaikan
                                        RuanganModel::insertOrIgnore($insert);
                                }

                                return response()->json([
                                        'status'  => true,
                                        'message' => 'Data berhasil diimport'
                                ]);
                        } else {
                                return response()->json([
                                        'status'  => false,
                                        'message' => 'Tidak ada data yang diimport'
                                ]);
                        }
                }

                return redirect('/');
        }

        public function export_excel()
        {
                //ambil data user yang akan di export
                $ruangan = RuanganModel::select('lantai_id', 'ruangan_kode', 'ruangan_nama')
                        ->orderBy('lantai_id')
                        ->with('lantai')
                        ->get();

                // load library excel
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

                $sheet->setCellValue('A1', 'No');
                $sheet->setCellValue('B1', 'Kode Ruangan');
                $sheet->setCellValue('C1', 'Nama Ruangan');
                $sheet->setCellValue('D1', 'Lantai');

                $sheet->getStyle('A1:D1')->getFont()->setBold(true); // bold header

                $no = 1;        // nomor data dimulai dari 1
                $baris = 2;     //baris data dimulai dari baris ke 2
                foreach ($ruangan as $key => $value) {
                        $sheet->setCellValue('A' . $baris, $no);
                        $sheet->setCellValue('B' . $baris, $value->ruangan_kode);
                        $sheet->setCellValue('C' . $baris, $value->ruangan_nama);
                        $sheet->setCellValue('D' . $baris, $value->lantai->lantai_nama); // ambil nama level
                        $baris++;
                        $no++;
                }

                foreach (range('A', 'D') as $columnID) {
                        $sheet->getColumnDimension($columnID)->setAutoSize(true); //set auto size untuk kolom
                }

                $sheet->setTitle('Data Ruangan'); // set title sheet
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $filename = 'Data Ruangan ' . date('Y-m-d H:i:s') . '.xlsx';
                header('Content-Type: application/vnd. openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public');
                $writer->save('php://output');
                exit;
        } // end function export_excel

        public function export_pdf()
        {
                $ruangan = RuanganModel::select('lantai_id', 'ruangan_kode', 'ruangan_nama')
                        ->orderBy('lantai_id')
                        ->with('lantai')
                        ->get();

                //use Barryvdh\DomPDF\Facade\Pdf;
                $pdf = Pdf::loadView('admin.ruangan.export_pdf', ['ruangan' => $ruangan]);
                $pdf->setPaper('a4', 'potrait'); //Set ukuran kertas dan orientasi
                $pdf->setOption('isRemoteEnabled', true); // set true jika ada gambar dari url
                $pdf->render();

                return $pdf->stream('Data Ruangan ' . date('Y-m-d H:i:s') . '.pdf');
        }
}
