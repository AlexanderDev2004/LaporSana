<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

trait ExcelImportTrait
{
    /**
     * Handle Excel file import with validation
     *
     * @param  string  $fileFieldName  Field name in the request (e.g., 'file_roles')
     * @param  callable  $dataMapper  Callback to map Excel row to database fields
     * @param  string  $modelClass  Model class name for insert
     * @return \Illuminate\Http\JsonResponse
     */
    protected function importExcel(
        Request $request,
        string $fileFieldName,
        callable $dataMapper,
        string $modelClass
    ) {
        if (! $request->ajax() && ! $request->wantsJson()) {
            return redirect('/');
        }

        $rules = [
            $fileFieldName => ['required', 'mimes:xlsx', 'max:1024'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors(),
            ]);
        }

        $file = $request->file($fileFieldName);

        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();

        $data = $sheet->toArray(null, false, true, true);

        $insert = [];

        if (count($data) > 1) {
            foreach ($data as $baris => $value) {
                if ($baris > 1) { // Skip header row
                    $mappedData = $dataMapper($value);
                    $mappedData['created_at'] = now();
                    $insert[] = $mappedData;
                }
            }

            if (count($insert) > 0) {
                $modelClass::insertOrIgnore($insert);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data berhasil diimport',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada data yang diimport',
            ]);
        }
    }
}
