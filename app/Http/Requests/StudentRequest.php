<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\BehaviourLevel;
use App\Enums\Gender;
use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StudentRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        $studentId = $this->boundId();

        return [
            // Matches the partial unique index: soft-deleted rows do not block reuse.
            'student_code' => [
                'required', 'string', 'max:20',
                Rule::unique('students', 'student_code')
                    ->ignore($studentId)
                    ->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string', 'max:150'],
            'gender' => ['required', new Enum(Gender::class)],
            'year_level' => ['required', 'integer', 'between:1,6'],
            'behaviour_level' => ['required', new Enum(BehaviourLevel::class)],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'national_id' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
            'special_needs' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
            'enrolled_on' => ['required', 'date'],

            // Either attach to an existing family or create one inline, so the admin
            // never has to think about the Keluarga entity as a separate step.
            // Both may be blank: an only child needs no family record at all.
            'family_id' => ['nullable', 'integer', 'exists:families,id'],
            'family_name' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'student_code' => 'kod pelajar',
            'name' => 'nama',
            'gender' => 'jantina',
            'year_level' => 'tahun',
            'behaviour_level' => 'tahap tingkah laku',
            'date_of_birth' => 'tarikh lahir',
            'national_id' => 'no. kad pengenalan',
            'enrolled_on' => 'tarikh daftar',
            'family_name' => 'nama keluarga',
        ];
    }

    /** Route model bindings are typed object|string, so narrow before use. */
    private function boundId(): ?int
    {
        $bound = $this->route('student');

        return $bound instanceof Student ? (int) $bound->getKey() : null;
    }
}
