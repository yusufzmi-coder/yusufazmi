<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            // 1 Sangat Lembut .. 5 Sangat Tegas. Drives where problem students go.
            'firmness' => ['required', 'integer', 'between:1,5'],
            'max_classes' => ['required', 'integer', 'between:1,20'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'emel',
            'phone' => 'telefon',
            'firmness' => 'ketegasan',
            'max_classes' => 'had kelas',
        ];
    }
}
