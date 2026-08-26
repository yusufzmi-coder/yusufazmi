<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EnrolmentSource;
use App\Enums\EnrolmentStatus;
use Database\Factories\EnrolmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $session_id
 * @property int $student_id
 * @property int $class_id
 * @property EnrolmentStatus $status
 * @property bool $is_pinned
 * @property EnrolmentSource $source
 * @property int|null $assignment_run_id
 * @property array<int, mixed>|null $placement_reason
 * @property string|null $placement_reason_text
 * @property float|null $placement_score
 * @property Carbon $joined_at
 * @property Carbon|null $left_at
 * @property-read Student $student
 * @property-read SchoolClass $schoolClass
 */
class Enrolment extends Model
{
    /** @use HasFactory<EnrolmentFactory> */
    use HasFactory;

    protected $fillable = [
        'session_id', 'student_id', 'class_id', 'status', 'is_pinned', 'source',
        'assignment_run_id', 'placement_reason', 'placement_reason_text',
        'placement_score', 'joined_at', 'left_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => EnrolmentStatus::class,
            'source' => EnrolmentSource::class,
            'is_pinned' => 'boolean',
            'placement_reason' => 'array',
            'placement_score' => 'float',
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Student, $this> */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /** @return BelongsTo<SchoolClass, $this> */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /** @return BelongsTo<AssignmentRun, $this> */
    public function assignmentRun(): BelongsTo
    {
        return $this->belongsTo(AssignmentRun::class);
    }
}
