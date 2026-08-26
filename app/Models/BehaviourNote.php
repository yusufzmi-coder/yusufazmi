<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BehaviourNoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $student_id
 * @property int|null $author_id
 * @property int $level_at_time
 * @property string $body
 * @property Carbon $occurred_on
 */
class BehaviourNote extends Model
{
    /** @use HasFactory<BehaviourNoteFactory> */
    use HasFactory;

    protected $fillable = ['student_id', 'author_id', 'level_at_time', 'body', 'occurred_on'];

    protected function casts(): array
    {
        return ['occurred_on' => 'date', 'level_at_time' => 'integer'];
    }

    /** @return BelongsTo<Student, $this> */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
