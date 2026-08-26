<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Assignment\Enums\RunStatus;
use App\Domain\Assignment\Enums\SolveMode;
use Database\Factories\AssignmentRunFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $session_id
 * @property SolveMode $mode
 * @property RunStatus $status
 * @property string $input_hash
 * @property array<int, mixed> $rules_snapshot
 * @property float|null $objective_score
 * @property array<string, int> $stats
 * @property int $seed
 * @property int|null $duration_ms
 * @property string|null $error
 * @property int $created_by
 * @property Carbon|null $committed_at
 * @property int|null $committed_by
 * @property Carbon $created_at
 * @property-read User|null $creator
 */
class AssignmentRun extends Model
{
    /** @use HasFactory<AssignmentRunFactory> */
    use HasFactory;

    protected $fillable = [
        'session_id', 'mode', 'status', 'input_hash', 'rules_snapshot', 'objective_score',
        'stats', 'seed', 'duration_ms', 'error', 'created_by', 'committed_at', 'committed_by',
    ];

    protected function casts(): array
    {
        return [
            'mode' => SolveMode::class,
            'status' => RunStatus::class,
            'rules_snapshot' => 'array',
            'stats' => 'array',
            'objective_score' => 'float',
            'committed_at' => 'datetime',
        ];
    }

    /** @return HasMany<AssignmentRunItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(AssignmentRunItem::class);
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
