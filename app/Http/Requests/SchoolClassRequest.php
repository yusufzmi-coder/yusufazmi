<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Day;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class SchoolClassRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        $sessionId = AcademicSession::active()->id;

        return [
            'year_level' => ['required', 'integer', 'between:1,6'],
            'stream' => ['required', 'string', 'max:20', 'regex:/^[A-Z]+$/'],
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
            'capacity_override' => ['nullable', 'integer', 'between:1,200'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],

            'meetings' => ['array', 'max:10'],
            'meetings.*.day' => ['required', new Enum(Day::class)],
            'meetings.*.time_slot_id' => [
                'required', 'integer',
                // A class can never be scheduled into the REHAT row. The closure form
                // is required: the scalar form casts false to '', which PostgreSQL
                // rejects for a boolean column.
                Rule::exists('time_slots', 'id')->where(fn ($q) => $q->where('is_break', false)),
            ],
            'meetings.*.room_id' => ['nullable', 'integer', 'exists:rooms,id'],

            'session_year_stream' => [
                Rule::unique('classes', 'year_level')->where(
                    fn ($q) => $q->where('session_id', $sessionId)
                        ->where('stream', $this->input('stream'))
                        ->whereNull('deleted_at'),
                )->ignore($this->boundId()),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'stream' => mb_strtoupper(trim((string) $this->input('stream'))),
            // Only used to hang the composite uniqueness rule off a field name.
            'session_year_stream' => $this->input('year_level'),
        ]);
    }

    /** @return list<callable> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $seen = [];

                foreach ((array) $this->input('meetings', []) as $index => $meeting) {
                    $key = ($meeting['day'] ?? '').'|'.($meeting['time_slot_id'] ?? '');

                    if (isset($seen[$key])) {
                        $validator->errors()->add(
                            "meetings.{$index}.day",
                            'Kelas ini sudah ada pertemuan pada hari dan waktu yang sama.',
                        );
                    }

                    $seen[$key] = true;
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'year_level' => 'tahun',
            'stream' => 'aliran',
            'teacher_id' => 'guru',
            'capacity_override' => 'had kapasiti',
            'session_year_stream' => 'kombinasi tahun dan aliran',
        ];
    }

    public function messages(): array
    {
        return [
            'stream.regex' => 'Aliran mesti huruf besar sahaja, contohnya ALPHA.',
            'session_year_stream.unique' => 'Kelas untuk tahun dan aliran ini sudah wujud.',
            'meetings.*.time_slot_id.exists' => 'Slot masa tidak sah — kelas tidak boleh diletak dalam waktu REHAT.',
        ];
    }

    /** Route model bindings are typed object|string, so narrow before use. */
    private function boundId(): ?int
    {
        $bound = $this->route('class');

        return $bound instanceof SchoolClass ? (int) $bound->getKey() : null;
    }
}
