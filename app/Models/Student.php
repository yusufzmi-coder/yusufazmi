<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BehaviourLevel;
use App\Enums\EnrolmentStatus;
use App\Enums\Gender;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property int $id
 * @property int|null $family_id
 * @property string $student_code
 * @property string $name
 * @property string|null $national_id
 * @property Gender $gender
 * @property Carbon|null $date_of_birth
 * @property int $year_level
 * @property BehaviourLevel $behaviour_level
 * @property string|null $special_needs
 * @property string|null $phone
 * @property bool $is_active
 * @property Carbon $enrolled_on
 * @property-read Family|null $family
 * @property-read Enrolment|null $activeEnrolment
 */
class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'family_id', 'student_code', 'name', 'national_id', 'gender', 'date_of_birth',
        'year_level', 'behaviour_level', 'special_needs', 'phone', 'is_active', 'enrolled_on',
    ];

    protected $hidden = ['national_id'];

    protected function casts(): array
    {
        return [
            'national_id' => 'encrypted',
            'gender' => Gender::class,
            'behaviour_level' => BehaviourLevel::class,
            'date_of_birth' => 'date',
            'enrolled_on' => 'date',
            'year_level' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * An allow-list, never logAll(). Otherwise the activity log quietly becomes an
     * unencrypted shadow copy of the most sensitive fields in the system.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'student_code', 'year_level', 'behaviour_level', 'is_active', 'family_id'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('pelajar')
            ->setDescriptionForEvent(fn (string $event): string => match ($event) {
                'created' => 'Pelajar baharu ditambah',
                'updated' => 'Pelajar dikemas kini',
                'deleted' => 'Pelajar dipadam',
                default => $event,
            });
    }

    /** @return BelongsTo<Family, $this> */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /** @return BelongsToMany<Guardian, $this> */
    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class)
            ->withPivot(['relationship', 'is_primary', 'can_pickup']);
    }

    /** @return HasMany<Enrolment, $this> */
    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }

    /** @return HasOne<Enrolment, $this> */
    public function activeEnrolment(): HasOne
    {
        return $this->hasOne(Enrolment::class)->where('status', EnrolmentStatus::Active->value);
    }

    /** @return HasMany<BehaviourNote, $this> */
    public function behaviourNotes(): HasMany
    {
        return $this->hasMany(BehaviourNote::class);
    }

    /** @param Builder<$this> $query */
    public function scopeAssignable(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
