<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TeacherFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property string|null $phone
 * @property string|null $email
 * @property int $firmness
 * @property int $max_classes
 * @property bool $is_active
 * @property string|null $notes
 * @property-read int $classes_count
 */
class Teacher extends Model
{
    /** @use HasFactory<TeacherFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = ['user_id', 'name', 'phone', 'email', 'firmness', 'max_classes', 'is_active', 'notes'];

    protected function casts(): array
    {
        return ['firmness' => 'integer', 'max_classes' => 'integer', 'is_active' => 'boolean'];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'firmness', 'is_active'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('guru')
            ->setDescriptionForEvent(fn (string $event): string => match ($event) {
                'created' => 'Guru baharu ditambah',
                'updated' => 'Guru dikemas kini',
                'deleted' => 'Guru dipadam',
                default => $event,
            });
    }

    public function firmnessLabel(): string
    {
        return match ($this->firmness) {
            1 => 'Sangat Lembut',
            2 => 'Lembut',
            3 => 'Sederhana',
            4 => 'Tegas',
            5 => 'Sangat Tegas',
            default => 'Sederhana',
        };
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<SchoolClass, $this> */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }
}
