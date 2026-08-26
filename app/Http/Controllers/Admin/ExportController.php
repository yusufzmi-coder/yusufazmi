<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\EnrolmentStatus;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Support\Export\CsvStream;
use App\Support\Export\Pdf;
use App\Support\TimetableGrid;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController
{
    public function timetablePdf(TimetableGrid $grid): Response
    {
        $session = AcademicSession::active();

        // Landscape: five day columns do not fit sensibly on portrait A4.
        return Pdf::download('pdf.jadual', [
            'tajuk' => 'Jadual Kelas Mingguan',
            'subtajuk' => "Sesi {$session->name}",
            'grid' => $grid->build($session->id),
        ], "jadual-kelas-{$session->name}.pdf", orientation: 'landscape');
    }

    public function classRosterPdf(SchoolClass $class): Response
    {
        $class->load('teacher:id,name');

        $students = $class->activeEnrolments()
            ->with('student:id,name,student_code,gender,behaviour_level')
            ->get()
            ->sortBy(fn ($e): string => $e->student->name)
            ->map(fn ($e): array => [
                'kod' => $e->student->student_code,
                'nama' => $e->student->name,
                'jantina' => $e->student->gender->value,
                'tingkah_laku' => $e->student->behaviour_level->label(),
                'disemat' => $e->is_pinned,
            ])
            ->values()
            ->all();

        return Pdf::download('pdf.senarai-kelas', [
            'tajuk' => "Senarai Pelajar — {$class->name}",
            'subtajuk' => ($class->teacher?->name ?? 'Tiada guru').' · '.count($students).' pelajar',
            'pelajar' => $students,
        ], "senarai-{$class->name}.pdf");
    }

    public function studentsCsv(): StreamedResponse
    {
        $sessionId = AcademicSession::active()->id;

        $rows = function (): iterable {
            $query = Student::query()
                ->with(['family:id,name', 'activeEnrolment.schoolClass:id,year_level,stream,name'])
                ->orderBy('year_level')
                ->orderBy('name');

            // Chunked so a large roll never has to sit in memory all at once.
            foreach ($query->lazy(500) as $student) {
                yield [
                    $student->student_code,
                    $student->name,
                    $student->gender->value,
                    $student->year_level,
                    $student->behaviour_level->label(),
                    $student->family?->name,
                    $student->activeEnrolment?->schoolClass?->name,
                    $student->is_active ? 'Aktif' : 'Tidak Aktif',
                    $student->enrolled_on->format('d/m/Y'),
                ];
            }
        };

        // Headers match the import mapping, so an export can be edited and re-imported.
        return CsvStream::download(
            'pelajar-'.now()->format('Ymd').'.csv',
            ['Kod', 'Nama', 'Jantina', 'Tahun', 'Tingkah Laku', 'Keluarga', 'Kelas', 'Status', 'Tarikh Daftar'],
            $rows(),
        );
    }

    public function statisticsCsv(): StreamedResponse
    {
        $sessionId = AcademicSession::active()->id;

        $rows = SchoolClass::query()
            ->where('session_id', $sessionId)
            ->with(['teacher:id,name,firmness', 'meetings.room:id,capacity'])
            ->withCount([
                'enrolments as pelajar' => fn ($q) => $q->where('status', EnrolmentStatus::Active->value),
                'enrolments as lelaki' => fn ($q) => $q
                    ->where('status', EnrolmentStatus::Active->value)
                    ->whereHas('student', fn ($q) => $q->where('gender', 'L')),
            ])
            ->orderBy('year_level')
            ->orderBy('stream')
            ->get()
            ->map(function (SchoolClass $c): array {
                $capacity = $c->effectiveCapacity();

                return [
                    $c->name,
                    $c->year_level,
                    $c->teacher?->name,
                    $c->teacher?->firmness,
                    $c->pelajar,
                    $capacity,
                    $capacity > 0 ? round($c->pelajar / $capacity * 100).'%' : '—',
                    $c->lelaki,
                    $c->pelajar - $c->lelaki,
                ];
            });

        return CsvStream::download(
            'statistik-kelas-'.now()->format('Ymd').'.csv',
            ['Kelas', 'Tahun', 'Guru', 'Ketegasan', 'Pelajar', 'Kapasiti', 'Penggunaan', 'Lelaki', 'Perempuan'],
            $rows,
        );
    }
}
