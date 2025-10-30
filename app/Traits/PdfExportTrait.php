<?php

namespace App\Traits;

use Barryvdh\DomPDF\Facade\Pdf;

trait PdfExportTrait
{
    /**
     * Generate and stream a PDF from a view
     *
     * @param  string  $view  View name
     * @param  array  $data  Data to pass to the view
     * @param  string  $filename  Filename for the PDF
     * @param  string  $orientation  Paper orientation ('portrait' or 'landscape')
     * @param  string  $paperSize  Paper size (default 'a4')
     * @return \Illuminate\Http\Response
     */
    protected function generatePdf(
        string $view,
        array $data,
        string $filename,
        string $orientation = 'portrait',
        string $paperSize = 'a4'
    ) {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper($paperSize, $orientation);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->render();

        return $pdf->stream($filename);
    }
}
