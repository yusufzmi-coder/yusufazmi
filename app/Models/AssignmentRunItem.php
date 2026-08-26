<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Assignment\Enums\ItemAction;
use Database\Factories\AssignmentRunItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $assignment_run_id
 * @property int $student_id
 * @property int|null $from_class_id
 * @property int|null $to_class_id
 * @property ItemAction $action
 * @property float|null $score
 * @property array<int, mixed> $reasons
 * @property string|null $reason_text
 * @property array<int, mixed> $violations
 * @property int $rank
 * @property-read Student $student
 * @property-read SchoolClass|null $toClass
 * @property-read SchoolClass|null $fromClass
 */
class AssignmentRunItem extends Model
{
    /** @use HasFactory<AssignmentRunItemFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'assignment_run_id', 'student_id', 'from_class_id', 'to_class_id',
        'action', 'score', 'reasons', 'reason_text', 'violations', 'rank',
    ];

    protected function casts(): array
    {
        return [
            'action' => ItemAction::class,
            'reasons' => 'array',
            'violations' => 'array',
            'score' => 'float',
            'rank' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<AssignmentRun, $this> */
    public function run(): BelongsTo
    {
        return $this->belongsTo(AssignmentRun::class, 'assignment_run_id');
    }

    /** @return BelongsTo<Student, $this> */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /** @return BelongsTo<SchoolClass, $this> */
    public function toClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'to_class_id');
    }

    /** @return BelongsTo<SchoolClass, $this> */
    public function fromClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'from_class_id');
    }
}
