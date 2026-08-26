<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Assignment\Enums\RuleKind;
use Database\Factories\RuleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property int $id
 * @property int|null $session_id
 * @property string $rule_type
 * @property string $name
 * @property RuleKind $kind
 * @property bool $is_active
 * @property int $weight
 * @property array<string, mixed>|null $config
 * @property array<string, mixed>|null $applies_to
 * @property int $sort_order
 */
class Rule extends Model
{
    /** @use HasFactory<RuleFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'session_id', 'rule_type', 'name', 'kind', 'is_active',
        'weight', 'config', 'applies_to', 'sort_order', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'kind' => RuleKind::class,
            'is_active' => 'boolean',
            'weight' => 'integer',
            'config' => 'array',
            'applies_to' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['rule_type', 'name', 'is_active', 'weight', 'config'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('peraturan')
            ->setDescriptionForEvent(fn (string $event): string => match ($event) {
                'created' => 'Peraturan baharu ditambah',
                'updated' => 'Peraturan dikemas kini',
                'deleted' => 'Peraturan dipadam',
                default => $event,
            });
    }

    /** @param Builder<$this> $query */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** @return BelongsTo<AcademicSession, $this> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }
}
