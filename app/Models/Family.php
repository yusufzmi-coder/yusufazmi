<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SiblingPolicy;
use Database\Factories\FamilyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * The sibling relation lives here rather than being derived from shared guardians:
 * the "penjaga" on an enrolment form is often a van driver or agent attached to
 * several unrelated children, which would produce phantom siblings.
 *
 * @property int $id
 * @property string $name
 * @property SiblingPolicy $sibling_policy
 * @property string|null $address
 * @property string|null $notes
 */
class Family extends Model
{
    /** @use HasFactory<FamilyFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'families';

    protected $fillable = ['name', 'sibling_policy', 'address', 'notes'];

    protected function casts(): array
    {
        return ['sibling_policy' => SiblingPolicy::class];
    }

    /** @return HasMany<Student, $this> */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /** @return HasMany<Guardian, $this> */
    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class);
    }
}
