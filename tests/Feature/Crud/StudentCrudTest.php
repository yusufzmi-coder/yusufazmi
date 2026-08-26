<?php

declare(strict_types=1);

use App\Enums\BehaviourLevel;
use App\Enums\EnrolmentStatus;
use App\Enums\Gender;
use App\Models\AcademicSession;
use App\Models\Enrolment;
use App\Models\Family;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->session = AcademicSession::factory()->active()->create();
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
    $this->actingAs($this->user);
});

function studentPayload(array $overrides = []): array
{
    return array_merge([
        'student_code' => 'P-0001',
        'name' => 'Ahmad Danish',
        'gender' => Gender::Lelaki->value,
        'year_level' => 4,
        'behaviour_level' => BehaviourLevel::Normal->value,
        'is_active' => true,
        'enrolled_on' => now()->toDateString(),
        'family_id' => null,
        'family_name' => null,
    ], $overrides);
}

it('creates a student', function () {
    $this->post('/pelajar', studentPayload())->assertRedirect(route('pelajar.index'));

    expect(Student::first()->name)->toBe('Ahmad Danish');
});

it('creates the family inline so siblings can be linked in one step', function () {
    $this->post('/pelajar', studentPayload(['family_name' => 'Keluarga Abdullah']));
    $this->post('/pelajar', studentPayload([
        'student_code' => 'P-0002',
        'name' => 'Aminah',
        'family_name' => 'Keluarga Abdullah',
    ]));

    // Same family name must attach to the SAME family, or they are not siblings.
    expect(Family::count())->toBe(1)
        ->and(Student::pluck('family_id')->unique())->toHaveCount(1);
});

it('refuses a duplicate student code', function () {
    $this->post('/pelajar', studentPayload());

    $this->post('/pelajar', studentPayload(['name' => 'Lain']))
        ->assertSessionHasErrors('student_code');
});

it('lets a soft-deleted code be reused', function () {
    // The unique index is partial (WHERE deleted_at IS NULL), so removing a student
    // must free their code again.
    $this->post('/pelajar', studentPayload());
    $student = Student::first();

    $this->delete("/pelajar/{$student->id}")->assertRedirect();

    $this->post('/pelajar', studentPayload(['name' => 'Pelajar Baharu']))
        ->assertSessionHasNoErrors();

    expect(Student::where('name', 'Pelajar Baharu')->exists())->toBeTrue();
});

it('closes the active enrolment when a student is removed', function () {
    $class = SchoolClass::factory()->create([
        'session_id' => $this->session->id,
        'year_level' => 4,
        'teacher_id' => Teacher::factory()->create()->id,
    ]);

    $this->post('/pelajar', studentPayload());
    $student = Student::first();

    Enrolment::create([
        'session_id' => $this->session->id,
        'student_id' => $student->id,
        'class_id' => $class->id,
        'status' => EnrolmentStatus::Active->value,
        'source' => 'manual',
        'joined_at' => now(),
    ]);

    $this->delete("/pelajar/{$student->id}");

    // History survives; only the active row is closed.
    expect(Enrolment::count())->toBe(1)
        ->and(Enrolment::first()->status)->toBe(EnrolmentStatus::Withdrawn)
        ->and(Enrolment::first()->left_at)->not->toBeNull();
});

it('updates a student', function () {
    $this->post('/pelajar', studentPayload());
    $student = Student::first();

    $this->put("/pelajar/{$student->id}", studentPayload([
        'name' => 'Nama Dikemas Kini',
        'behaviour_level' => BehaviourLevel::Kritikal->value,
    ]))->assertRedirect(route('pelajar.index'));

    expect($student->fresh()->name)->toBe('Nama Dikemas Kini')
        ->and($student->fresh()->behaviour_level)->toBe(BehaviourLevel::Kritikal);
});

it('rejects a year level outside 1 to 6', function () {
    $this->post('/pelajar', studentPayload(['year_level' => 9]))
        ->assertSessionHasErrors('year_level');
});
