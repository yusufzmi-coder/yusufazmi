<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Integration;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Integration> */
class IntegrationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => 'sendscape',
            'enabled' => false,
            'config' => [],
        ];
    }
}
