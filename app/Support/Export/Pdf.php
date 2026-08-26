<?php

declare(strict_types=1);

namespace App\Support\Export;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/** Thin wrapper over dompdf: render a Blade view, hand back a download. */
final class Pdf
{
    /** @param array<string, mixed> $data */
    public static function download(string $view, array $data, string $filename, string $orientation = 'portrait'): Response
    {
        $options = new Options;
        $options->set('isRemoteEnabled', false);   // nothing in these documents is remote
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(View::make($view, $data)->render(), 'UTF-8');
        $dompdf->setPaper('a4', $orientation);
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
