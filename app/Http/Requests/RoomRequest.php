<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:60',
                Rule::unique('rooms', 'name')
                    ->ignore($this->boundId())
                    ->whereNull('deleted_at'),
            ],
            'capacity' => ['required', 'integer', 'between:1,200'],
            'location' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nama bilik', 'capacity' => 'kapasiti', 'location' => 'lokasi'];
    }

    /** Route model bindings are typed object|string, so narrow before use. */
    private function boundId(): ?int
    {
        $bound = $this->route('room');

        return $bound instanceof Room ? (int) $bound->getKey() : null;
    }
}
