<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property int $capacity
 * @property string|null $location
 * @property bool $is_active
 * @property-read int $class_meetings_count
 */
class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'capacity', 'location', 'is_active'];

    protected function casts(): array
    {
        return ['capacity' => 'integer', 'is_active' => 'boolean'];
    }

    /** @return HasMany<ClassMeeting, $this> */
    public function classMeetings(): HasMany
    {
        return $this->hasMany(ClassMeeting::class);
    }
}
