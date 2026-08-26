<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TimeSlotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $label
 * @property string $starts_at
 * @property string $ends_at
 * @property bool $is_break
 * @property int $sort_order
 */
class TimeSlot extends Model
{
    /** @use HasFactory<TimeSlotFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['label', 'starts_at', 'ends_at', 'is_break', 'sort_order'];

    protected function casts(): array
    {
        return ['is_break' => 'boolean', 'sort_order' => 'integer'];
    }

    /** @return HasMany<SchoolClass, $this> */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }
}
