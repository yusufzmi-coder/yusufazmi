<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\BehaviourLevel;
use App\Enums\Gender;
use App\Enums\SiblingPolicy;
use App\Models\Family;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

/**
 * The way out of Google Sheets, so it has to be forgiving about how the sheet is
 * laid out and unforgiving about what it writes: every row is validated, and a bad
 * row is reported with its line number rather than silently skipped.
 */
final class StudentImporter
{
    /** Destination field => the header spellings seen in real exports. */
    public const FIELDS = [
        'student_code' => ['kod', 'kod pelajar', 'no pendaftaran', 'no. pendaftaran', 'student code', 'id'],
        'name' => ['nama', 'nama pelajar', 'name', 'nama penuh'],
        'gender' => ['jantina', 'gender', 'l/p'],
        'year_level' => ['tahun', 'darjah', 'year', 'year level'],
        'behaviour_level' => ['tingkah laku', 'tahap tingkah laku', 'behaviour', 'attitude'],
        'date_of_birth' => ['tarikh lahir', 'dob', 'date of birth'],
        'national_id' => ['no kp', 'no. kp', 'kad pengenalan', 'ic', 'nric'],
        'phone' => ['telefon', 'no telefon', 'phone'],
        'family_name' => ['keluarga', 'nama keluarga', 'family'],
    ];

    /** @return array{headers: list<string>, rows: list<array<string, string>>} */
    public function read(string $path, string $extension): array
    {
        $reader = mb_strtolower($extension) === 'xlsx' ? new XlsxReader : new CsvReader;
        $reader->open($path);

        $headers = [];
        $rows = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {
                // openspout v5 exposes the row via toArray(); getCells() returned Cell
                // objects in v4 and no longer exists. A cell can hold a date or a rich
                // text run, neither of which casts to string cleanly.
                $cells = array_map(
                    static fn (mixed $value): string => match (true) {
                        $value instanceof \DateTimeInterface => $value->format('Y-m-d'),
                        is_scalar($value) => trim((string) $value),
                        default => '',
                    },
                    $row->toArray(),
                );

                if ($index === 1) {
                    $headers = $cells;

                    continue;
                }

                if (implode('', $cells) === '') {
                    continue;   // blank spacer row
                }

                $assoc = [];
                foreach ($headers as $i => $header) {
                    $assoc[$header] = $cells[$i] ?? '';
                }

                $rows[] = $assoc;
            }

            break;   // first sheet only
        }

        $reader->close();

        return ['headers' => array_values($headers), 'rows' => $rows];
    }

    /**
     * Best-effort header matching so the admin usually just confirms the mapping.
     *
     * @param  list<string>  $headers
     * @return array<string, string|null>
     */
    public function guessMapping(array $headers): array
    {
        $mapping = [];

        foreach (self::FIELDS as $field => $aliases) {
            $mapping[$field] = null;

            foreach ($headers as $header) {
                if (in_array(mb_strtolower(trim($header)), $aliases, strict: true)) {
                    $mapping[$field] = $header;

                    break;
                }
            }
        }

        return $mapping;
    }

    /**
     * Validates every row without writing anything, so the admin sees the damage
     * before committing to it.
     *
     * @param  list<array<string, string>>  $rows
     * @param  array<string, string|null>  $mapping
     * @return array{valid: list<array<string, mixed>>, errors: list<array{baris: int, ralat: list<string>}>}
     */
    public function validate(array $rows, array $mapping): array
    {
        $valid = [];
        $errors = [];
        $seenCodes = [];

        foreach ($rows as $i => $row) {
            $line = $i + 2;   // header occupies line 1
            $data = $this->extract($row, $mapping);

            $validator = Validator::make($data, [
                'student_code' => ['required', 'string', 'max:20'],
                'name' => ['required', 'string', 'max:150'],
                'gender' => ['required', 'in:L,P'],
                'year_level' => ['required', 'integer', 'between:1,6'],
                'behaviour_level' => ['required', 'integer', 'between:0,3'],
                'date_of_birth' => ['nullable', 'date'],
                'national_id' => ['nullable', 'string', 'max:20'],
                'phone' => ['nullable', 'string', 'max:20'],
                'family_name' => ['nullable', 'string', 'max:120'],
            ], [], [
                'student_code' => 'kod pelajar',
                'name' => 'nama',
                'gender' => 'jantina',
                'year_level' => 'tahun',
                'behaviour_level' => 'tahap tingkah laku',
            ]);

            $messages = $validator->fails() ? array_values($validator->errors()->all()) : [];

            // Duplicates within the file itself would otherwise fail mid-transaction.
            if (filled($data['student_code']) && isset($seenCodes[$data['student_code']])) {
                $messages[] = sprintf(
                    'Kod pelajar %s berulang (baris %d).',
                    $data['student_code'],
                    $seenCodes[$data['student_code']],
                );
            }

            if ($messages !== []) {
                $errors[] = ['baris' => $line, 'ralat' => $messages];

                continue;
            }

            $seenCodes[$data['student_code']] = $line;
            $data['baris'] = $line;
            $valid[] = $data;
        }

        return ['valid' => $valid, 'errors' => $errors];
    }

    /**
     * Existing codes are updated rather than duplicated, so re-importing a corrected
     * sheet is safe.
     *
     * @param  list<array<string, mixed>>  $rows
     * @return array{dicipta: int, dikemaskini: int}
     */
    public function commit(array $rows): array
    {
        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($rows, &$created, &$updated): void {
            foreach ($rows as $row) {
                $familyId = null;

                if (filled($row['family_name'])) {
                    $familyId = Family::firstOrCreate(
                        ['name' => $row['family_name']],
                        ['sibling_policy' => SiblingPolicy::Inherit],
                    )->id;
                }

                $existing = Student::withTrashed()
                    ->where('student_code', $row['student_code'])
                    ->first();

                $attributes = [
                    'name' => $row['name'],
                    'gender' => Gender::from($row['gender']),
                    'year_level' => (int) $row['year_level'],
                    'behaviour_level' => BehaviourLevel::from((int) $row['behaviour_level']),
                    'date_of_birth' => $row['date_of_birth'] ?: null,
                    'national_id' => $row['national_id'] ?: null,
                    'phone' => $row['phone'] ?: null,
                    'family_id' => $familyId,
                ];

                if ($existing !== null) {
                    $existing->restore();
                    $existing->update($attributes);
                    $updated++;

                    continue;
                }

                Student::create([
                    ...$attributes,
                    'student_code' => $row['student_code'],
                    'is_active' => true,
                    'enrolled_on' => now(),
                ]);
                $created++;
            }
        });

        return ['dicipta' => $created, 'dikemaskini' => $updated];
    }

    /**
     * @param  array<string, string>  $row
     * @param  array<string, string|null>  $mapping
     * @return array<string, mixed>
     */
    private function extract(array $row, array $mapping): array
    {
        $value = fn (string $field): string => trim((string) ($row[$mapping[$field] ?? ''] ?? ''));

        return [
            'student_code' => $value('student_code'),
            'name' => $value('name'),
            'gender' => $this->normaliseGender($value('gender')),
            'year_level' => $value('year_level') === '' ? null : (int) $value('year_level'),
            'behaviour_level' => $this->normaliseBehaviour($value('behaviour_level')),
            'date_of_birth' => $value('date_of_birth') ?: null,
            'national_id' => $value('national_id'),
            'phone' => $value('phone'),
            'family_name' => $value('family_name'),
        ];
    }

    /** Sheets in the wild write "Lelaki", "L", "M", "Male" -- all mean the same thing. */
    private function normaliseGender(string $raw): string
    {
        return match (mb_strtolower($raw)) {
            'l', 'lelaki', 'm', 'male', 'laki-laki' => 'L',
            'p', 'perempuan', 'f', 'female' => 'P',
            default => $raw,
        };
    }

    /** Accepts either the numeric level or its Malay label. */
    private function normaliseBehaviour(string $raw): int
    {
        if ($raw === '') {
            return 0;
        }

        if (is_numeric($raw)) {
            return (int) $raw;
        }

        return match (mb_strtolower($raw)) {
            'normal', 'baik' => 0,
            'perlu perhatian' => 1,
            'bermasalah' => 2,
            'kritikal' => 3,
            default => -1,   // fails validation, and says so
        };
    }
}
