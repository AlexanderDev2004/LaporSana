<?php

namespace App\Http\Controllers;

use App\Models\FasilitasModel;
use App\Models\LaporanDetail;
use App\Models\LaporanModel;
use App\Models\StatusModel;
use App\Models\TugasModel;
use App\Models\TugasDetail;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Laporan',
            'list'  => ['Home', 'Laporan']
        ];

        $active_menu = 'laporan';
        $user = UserModel::all();
        $status = StatusModel::all();
        $details = LaporanDetail::all();



        return view('admin.validasi_laporan.index', compact('breadcrumb', 'active_menu', 'user', 'status', 'details'));
    }

    public function list(Request $request)
    {
        $laporan = LaporanModel::select('laporan_id', 'user_id', 'status_id', 'tanggal_lapor', 'jumlah_pelapor')
            ->with('user', 'status');
        if ($request->user_id) {
            $laporan->where('user_id', $request->user_id);
        }
        if ($request->status_id) {
            $laporan->where('status_id', $request->status_id);
        }

        return DataTables::of($laporan)
            ->addIndexColumn()
            ->addColumn('aksi', function ($laporan) {
                $btn = '<button onclick="modalAction(\'' . route('admin.validasi_laporan.show', $laporan->laporan_id) . '\')" class="btn btn-info btn-sm mr-1"><i class="fas fa-eye"></i></button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }



    public function show($laporan_id)
    {
        $laporan = LaporanModel::with(['details.fasilitas.ruangan.lantai', 'status', 'user'])
            ->where('laporan_id', $laporan_id)
            ->firstOrFail();

        return view('admin.validasi_laporan.show', compact('laporan'));
    }


    public function verify(Request $request, $laporan_id)
    {
        $laporan = LaporanModel::findOrFail($laporan_id);

        if ($request->verifikasi == 'tolak') {
            $laporan->status_id = 2; // Status ditolak
            $laporan->save();

            // Notify user of rejection (optional)
            // $laporan->user->notify(new LaporanStatusUpdated($laporan, 'Ditolak'));

            return response()->json(['status' => true, 'message' => 'Laporan berhasil ditolak']);
        } elseif ($request->verifikasi == 'setuju') {
            $laporan->status_id = 5; // Status disetujui
            $laporan->save();

            return response()->json(['status' => true, 'message' => 'Laporan berhasil disetujui']);
        }

        return response()->json(['status' => false, 'message' => 'Aksi tidak valid']);
    }

    public function export_excel()
    {
        //ambil data user yang akan di export
        $laporan = LaporanModel::select('user_id', 'status_id', 'tanggal_lapor', 'jumlah_pelapor', 'laporan_id')
            ->orderBy('user_id')
            ->with('user', 'status', 'details.fasilitas')
            ->get();


        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Pelapor');
        $sheet->setCellValue('C1', 'Status');
        $sheet->setCellValue('D1', 'Tanggal Lapor');
        $sheet->setCellValue('E1', 'Jumlah Pelapor');
        $sheet->setCellValue('F1', 'Fasilitas');
        $sheet->setCellValue('G1', 'Foto Bukti');
        $sheet->setCellValue('H1', 'Deskripsi');

        $sheet->getStyle('A1:H1')->getFont()->setBold(true); // bold header

        $no = 1;        // nomor data dimulai dari 1
        $baris = 2;     //baris data dimulai dari baris ke 2
        foreach ($laporan as $key => $value) {
            $detail = $value->details->first();
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $value->user->name);
            $sheet->setCellValue('C' . $baris, $value->status->status_nama);
            $sheet->setCellValue('D' . $baris, $value->tanggal_lapor);
            $sheet->setCellValue('E' . $baris, $value->jumlah_pelapor);
            $sheet->setCellValue('F' . $baris, ($detail) ? $detail->fasilitas->fasilitas_nama : '');
            $sheet->setCellValue('G' . $baris, ($detail) ? $detail->foto_bukti : '');
            $sheet->setCellValue('H' . $baris, ($detail) ? $detail->deskripsi : '');
            $baris++;
            $no++;
        }

        foreach (range('A', 'H') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true); //set auto size untuk kolom
        }

        $sheet->setTitle('Data Laporan Kerusakan'); // set title sheet
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Laporan Kerusakan' . date('Y-m-d H:i:s') . '.xlsx';
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
        $laporan = LaporanModel::select('user_id', 'status_id', 'tanggal_lapor', 'jumlah_pelapor', 'laporan_id')
            ->orderBy('user_id')
            ->with('user', 'status', 'details.fasilitas')
            ->get();

        //use Barryvdh\DomPDF\Facade\Pdf;
        $pdf = Pdf::loadView('admin.validasi_laporan.export_pdf', ['laporan' => $laporan]);
        $pdf->setPaper('a4', 'potrait'); //Set ukuran kertas dan orientasi
        $pdf->setOption('isRemoteEnabled', true); // set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data Laporan Kerusakan ' . date('Y-m-d H:i:s') . '.pdf');
    }
}
