<?php

declare(strict_types=1);

namespace App\Support\Export;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a CSV rather than building it in memory, so exporting every student stays
 * flat in memory regardless of how many there are.
 */
final class CsvStream
{
    /**
     * @param  list<string>  $headers
     * @param  iterable<int, array<int|string, scalar|null>>  $rows
     */
    public static function download(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows): void {
            $out = fopen('php://output', 'wb');

            if ($out === false) {
                return;
            }

            // Excel opens UTF-8 CSVs as mojibake without a BOM, and these files are
            // going straight back into a spreadsheet.
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, $headers);

            foreach ($rows as $row) {
                fputcsv($out, array_map(
                    static fn ($v): string => $v === null ? '' : (string) $v,
                    array_values($row),
                ));
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
