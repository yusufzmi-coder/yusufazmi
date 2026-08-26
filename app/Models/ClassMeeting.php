<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Day;
use Database\Factories\ClassMeetingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One cell in the weekly grid.
 *
 * @property int $id
 * @property int $class_id
 * @property int $session_id
 * @property int|null $teacher_id
 * @property int|null $room_id
 * @property Day $day
 * @property int $time_slot_id
 * @property-read SchoolClass $schoolClass
 * @property-read Room|null $room
 * @property-read TimeSlot|null $timeSlot
 */
class ClassMeeting extends Model
{
    /** @use HasFactory<ClassMeetingFactory> */
    use HasFactory;

    protected $fillable = ['class_id', 'session_id', 'teacher_id', 'room_id', 'day', 'time_slot_id'];

    protected function casts(): array
    {
        return ['day' => Day::class];
    }

    /** @return BelongsTo<SchoolClass, $this> */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /** @return BelongsTo<Room, $this> */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** @return BelongsTo<Teacher, $this> */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /** @return BelongsTo<TimeSlot, $this> */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }
}
