<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EnrolmentStatus;
use Database\Factories\SchoolClassFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * `class` is a reserved word in PHP, so the model is SchoolClass while the table
 * keeps its natural name.
 *
 * @property int $id
 * @property int $session_id
 * @property int $year_level
 * @property string $stream
 * @property string $name Generated column in PostgreSQL: year_level || ' ' || stream.
 * @property int|null $teacher_id
 * @property int|null $capacity_override
 * @property bool $is_active
 * @property string|null $notes
 * @property-read Teacher|null $teacher
 * @property-read Collection<int, ClassMeeting> $meetings
 * @property-read int $pelajar
 * @property-read int $lelaki
 */
class SchoolClass extends Model
{
    /** @use HasFactory<SchoolClassFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'session_id', 'year_level', 'stream', 'teacher_id',
        'capacity_override', 'is_active', 'notes',
    ];

    // Generated always in PostgreSQL -- never write to it.
    protected $guarded = ['name'];

    protected function casts(): array
    {
        return [
            'year_level' => 'integer',
            'capacity_override' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['year_level', 'stream', 'teacher_id', 'capacity_override', 'is_active'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('kelas')
            ->setDescriptionForEvent(fn (string $event): string => match ($event) {
                'created' => 'Kelas baharu ditambah',
                'updated' => 'Kelas dikemas kini',
                'deleted' => 'Kelas dipadam',
                default => $event,
            });
    }

    /**
     * The physical ceiling. Without an override it is the SMALLEST room the class
     * meets in -- the same students attend every meeting, so the tightest room wins.
     */
    public function effectiveCapacity(): int
    {
        if ($this->capacity_override !== null) {
            return $this->capacity_override;
        }

        $capacities = $this->meetings
            ->map(fn (ClassMeeting $m): ?int => $m->room?->capacity)
            ->filter()
            ->all();

        return $capacities === [] ? 0 : (int) min($capacities);
    }

    /** @return BelongsTo<AcademicSession, $this> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    /** @return BelongsTo<Teacher, $this> */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /** @return HasMany<ClassMeeting, $this> */
    public function meetings(): HasMany
    {
        return $this->hasMany(ClassMeeting::class, 'class_id');
    }

    /** @return HasMany<Enrolment, $this> */
    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class, 'class_id');
    }

    /** @return HasMany<Enrolment, $this> */
    public function activeEnrolments(): HasMany
    {
        return $this->enrolments()->where('status', EnrolmentStatus::Active->value);
    }
}
