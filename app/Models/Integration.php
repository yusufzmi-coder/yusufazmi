<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\IntegrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $key
 * @property bool $enabled
 * @property array<string, string>|null $config
 * @property string|null $last_test_status
 * @property string|null $last_test_message
 * @property Carbon|null $last_tested_at
 */
class Integration extends Model
{
    /** @use HasFactory<IntegrationFactory> */
    use HasFactory;

    protected $fillable = ['key', 'enabled', 'config', 'last_test_status', 'last_test_message', 'last_tested_at'];

    protected $hidden = ['config'];

    protected function casts(): array
    {
        return [
            // Credentials the admin typed in never sit in the database as plaintext.
            'config' => 'encrypted:array',
            'enabled' => 'boolean',
            'last_tested_at' => 'datetime',
        ];
    }
}
