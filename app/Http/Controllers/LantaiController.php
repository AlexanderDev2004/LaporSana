<?php

namespace App\Http\Controllers;

use App\Models\LantaiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function import()
        {
                return view('admin.lantai.import');
        }

     public function import_ajax(Request $request)
        {
                if ($request->ajax() || $request->wantsJson()) {
                        $rules = [
                                // validasi file harus xls atau xlsx, max 1MB
                                'file_lantai' => ['required', 'mimes:xlsx', 'max:1024']
                        ];

                        $validator = Validator::make($request->all(), $rules);

                        if ($validator->fails()) {
                                return response()->json([
                                        'status' => false,
                                        'message' => 'Validasi Gagal',
                                        'msgField' => $validator->errors()
                                ]);
                        }

                        $file = $request->file('file_lantai'); // ambil file dari request

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
                                                        'lantai_kode'    => $value['A'],
                                                        'lantai_nama'    => $value['B'],
                                                        'created_at'    => now(),
                                                ];
                                        }
                                }

                                if (count($insert) > 0) {
                                        // insert data ke database, jika data sudah ada, maka diabaikan
                                        LantaiModel::insertOrIgnore($insert);
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
                //ambil data role yang akan di export
                $lantai = LantaiModel::select('lantai_kode', 'lantai_nama')
                        ->orderBy('lantai_nama')
                        ->get();

                // load library excel
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

                $sheet->setCellValue('A1', 'No');
                $sheet->setCellValue('B1', 'Role Kode');
                $sheet->setCellValue('C1', 'Role Nama');

                $sheet->getStyle('A1:C1')->getFont()->setBold(true); // bold header

                $no = 1;        // nomor data dimulai dari 1
                $baris = 2;     //baris data dimulai dari baris ke 2
                foreach ($lantai as $key => $value) {
                        $sheet->setCellValue('A' . $baris, $no);
                        $sheet->setCellValue('B' . $baris, $value->lantai_kode);
                        $sheet->setCellValue('C' . $baris, $value->lantai_nama);
                        $baris++;
                        $no++;
                }

                foreach (range('A', 'C') as $columnID) {
                        $sheet->getColumnDimension($columnID)->setAutoSize(true); //set auto size untuk kolom
                }

                $sheet->setTitle('Data Lantai'); // set title sheet
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $filename = 'Data Lantai ' . date('Y-m-d H:i:s') . '.xlsx';
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
                 $lantai = LantaiModel::select('lantai_kode', 'lantai_nama')
                        ->orderBy('lantai_nama')
                        ->get();

                //use Barryvdh\DomPDF\Facade\Pdf;
                $pdf = Pdf::loadView('admin.lantai.export_pdf', ['lantai' => $lantai]);
                $pdf->setPaper('a4', 'potrait'); //Set ukuran kertas dan orientasi
                $pdf->setOption('isRemoteEnabled', true); // set true jika ada gambar dari url
                $pdf->render();

                return $pdf->stream('Data Lantai ' . date('Y-m-d H:i:s') . '.pdf');
        }
}
