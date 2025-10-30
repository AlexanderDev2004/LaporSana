<?php

namespace App\Traits;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

trait ExcelExportTrait
{
    /**
     * Generate Excel file headers for download
     *
     * @return void
     */
    protected function setExcelHeaders(string $filename)
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
    }

    /**
     * Create spreadsheet with headers and data
     *
     * @param  array  $headers  Column headers (e.g., ['A' => 'No', 'B' => 'Name'])
     * @param  array  $data  Array of data rows
     * @param  string  $sheetTitle  Title for the sheet
     * @return Spreadsheet
     */
    protected function createSpreadsheet(array $headers, array $data, string $sheetTitle)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        foreach ($headers as $column => $header) {
            $sheet->setCellValue($column.'1', $header);
        }

        // Make header bold
        $lastColumn = array_key_last($headers);
        $sheet->getStyle('A1:'.$lastColumn.'1')->getFont()->setBold(true);

        // Set data
        $baris = 2;
        foreach ($data as $row) {
            foreach ($row as $column => $value) {
                $sheet->setCellValue($column.$baris, $value);
            }
            $baris++;
        }

        // Auto-size columns
        foreach (array_keys($headers) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle($sheetTitle);

        return $spreadsheet;
    }

    /**
     * Export spreadsheet to browser
     *
     * @return void
     */
    protected function exportSpreadsheet(Spreadsheet $spreadsheet, string $filename)
    {
        $this->setExcelHeaders($filename);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
