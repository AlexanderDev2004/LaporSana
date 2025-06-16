<?php

namespace App\Http\Controllers;

use App\Models\LantaiModel;
use App\Models\FasilitasModel;
use App\Models\RuanganModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
                'tingkat_urgensi' => 'required|in:1,2,3,4,5',
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
                $fasilitas->tingkat_urgensi = $request->tingkat_urgensi;
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
                'tingkat_urgensi' => 'required|in:1,2,3,4,5', // Validasi tingkat urgensi
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

    public function import()
        {
                return view('admin.fasilitas.import');
        }

        public function import_ajax(Request $request)
        {
                if ($request->ajax() || $request->wantsJson()) {
                        $rules = [
                                // validasi file harus xls atau xlsx, max 1MB
                                'file_fasilitas' => ['required', 'mimes:xlsx', 'max:1024']
                        ];

                        $validator = Validator::make($request->all(), $rules);

                        if ($validator->fails()) {
                                return response()->json([
                                        'status' => false,
                                        'message' => 'Validasi Gagal',
                                        'msgField' => $validator->errors()
                                ]);
                        }

                        $file = $request->file('file_fasilitas'); // ambil file dari request

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
                                                        'ruangan_id'        => $value['A'],
                                                        'fasilitas_kode'    => $value['B'],
                                                        'fasilitas_nama'    => $value['C'],
                                                        'tingkat_urgensi'   => $value['D'],
                                                        'created_at'     => now(),
                                                ];
                                        }
                                }

                                if (count($insert) > 0) {
                                        // insert data ke database, jika data sudah ada, maka diabaikan
                                        FasilitasModel::insertOrIgnore($insert);
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
                $fasilitas = FasilitasModel::select('ruangan_id', 'fasilitas_kode', 'fasilitas_nama', 'tingkat_urgensi')
                        ->orderBy('fasilitas_nama')
                        ->with('ruangan')
                        ->get();

                // load library excel
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

                $sheet->setCellValue('A1', 'No');
                $sheet->setCellValue('B1', 'Fasilitas Kode');
                $sheet->setCellValue('C1', 'Fasilitas Nama');
                $sheet->setCellValue('D1', 'Nama Ruangan');
                $sheet->setCellValue('E1', 'Tingkat Urgensi');

                $sheet->getStyle('A1:E1')->getFont()->setBold(true); // bold header

                $no = 1;        // nomor data dimulai dari 1
                $baris = 2;     //baris data dimulai dari baris ke 2
                foreach ($fasilitas as $key => $value) {
                        $sheet->setCellValue('A' . $baris, $no);
                        $sheet->setCellValue('B' . $baris, $value->fasilitas_kode);
                        $sheet->setCellValue('C' . $baris, $value->fasilitas_nama);
                        $sheet->setCellValue('D' . $baris, $value->ruangan->ruangan_nama);
                        $sheet->setCellValue('E' . $baris, $value->tingkat_urgensi);
                        $baris++;
                        $no++;
                }

                foreach (range('A', 'E') as $columnID) {
                        $sheet->getColumnDimension($columnID)->setAutoSize(true); //set auto size untuk kolom
                }

                $sheet->setTitle('Data Fasilitas'); // set title sheet
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $filename = 'Data Fasilitas ' . date('Y-m-d H:i:s') . '.xlsx';
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
                $fasilitas = FasilitasModel::select('ruangan_id', 'fasilitas_kode', 'fasilitas_nama', 'tingkat_urgensi')
                        ->orderBy('fasilitas_nama')
                        ->with('ruangan')
                        ->get();


                //use Barryvdh\DomPDF\Facade\Pdf;
                $pdf = Pdf::loadView('admin.fasilitas.export_pdf', ['fasilitas' => $fasilitas]);
                $pdf->setPaper('a4', 'potrait'); //Set ukuran kertas dan orientasi
                $pdf->setOption('isRemoteEnabled', true); // set true jika ada gambar dari url
                $pdf->render();

                return $pdf->stream('Data Fasilitas ' . date('Y-m-d H:i:s') . '.pdf');
        }
}
