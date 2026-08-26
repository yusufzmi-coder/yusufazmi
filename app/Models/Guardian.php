<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\GuardianFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int|null $family_id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property string|null $national_id
 * @property string|null $occupation
 */
class Guardian extends Model
{
    /** @use HasFactory<GuardianFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['family_id', 'name', 'phone', 'email', 'national_id', 'occupation'];

    protected $hidden = ['national_id'];

    protected function casts(): array
    {
        // Encrypted at rest, and therefore not searchable. That is an accepted trade.
        return ['national_id' => 'encrypted'];
    }

    /** @return BelongsTo<Family, $this> */
    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /** @return BelongsToMany<Student, $this> */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)
            ->withPivot(['relationship', 'is_primary', 'can_pickup']);
    }
}
