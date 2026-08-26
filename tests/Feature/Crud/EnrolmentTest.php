<?php

declare(strict_types=1);

use App\Enums\EnrolmentSource;
use App\Enums\EnrolmentStatus;
use App\Models\AcademicSession;
use App\Models\ClassMeeting;
use App\Models\Enrolment;
use App\Models\Room;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TimeSlot;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TimeSlotSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(TimeSlotSeeder::class);

    $this->session = AcademicSession::factory()->active()->create();
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
    $this->actingAs($this->user);

    $slot = TimeSlot::where('is_break', false)->orderBy('sort_order')->first();

    $this->makeClass = function (string $stream, int $capacity, string $day, int $year = 4) use ($slot): SchoolClass {
        $class = SchoolClass::factory()->create([
            'session_id' => $this->session->id,
            'year_level' => $year,
            'stream' => $stream,
            'teacher_id' => Teacher::factory()->create()->id,
            'capacity_override' => null,
        ]);

        ClassMeeting::create([
            'class_id' => $class->id,
            'session_id' => $this->session->id,
            'teacher_id' => $class->teacher_id,
            'room_id' => Room::factory()->create(['capacity' => $capacity])->id,
            'day' => $day,
            'time_slot_id' => $slot->id,
        ]);

        return $class->fresh();
    };

    $this->alpha = ($this->makeClass)('ALPHA', 2, 'isnin');
    $this->beta = ($this->makeClass)('BETA', 5, 'selasa');
    $this->student = Student::factory()->create(['year_level' => 4, 'name' => 'Ahmad']);
});

it('places a student into a class by hand', function () {
    $this->post("/jadual/kelas/{$this->alpha->id}/pelajar", ['student_id' => $this->student->id])
        ->assertRedirect();

    $enrolment = Enrolment::where('status', EnrolmentStatus::Active->value)->first();

    expect($enrolment->class_id)->toBe($this->alpha->id)
        ->and($enrolment->source)->toBe(EnrolmentSource::Manual);
});

it('refuses a student from the wrong year level', function () {
    // The manual path honours the same hard constraints as the engine; otherwise it
    // could build a roster the solver considers impossible.
    $wrongYear = Student::factory()->create(['year_level' => 2, 'name' => 'Siti']);

    $this->post("/jadual/kelas/{$this->alpha->id}/pelajar", ['student_id' => $wrongYear->id])
        ->assertSessionHas('error', fn (string $m): bool => str_contains($m, 'Tahun 2'));

    expect(Enrolment::count())->toBe(0);
});

it('refuses to overfill a class', function () {
    foreach (range(1, 2) as $i) {
        $s = Student::factory()->create(['year_level' => 4]);
        $this->post("/jadual/kelas/{$this->alpha->id}/pelajar", ['student_id' => $s->id]);
    }

    $this->post("/jadual/kelas/{$this->alpha->id}/pelajar", ['student_id' => $this->student->id])
        ->assertSessionHas('error', fn (string $m): bool => str_contains($m, 'penuh'));

    expect(Enrolment::where('status', EnrolmentStatus::Active->value)->count())->toBe(2);
});

it('moves a student and keeps the old row as history', function () {
    $this->post("/jadual/kelas/{$this->alpha->id}/pelajar", ['student_id' => $this->student->id]);
    $enrolment = Enrolment::first();

    $this->patch("/enrolan/{$enrolment->id}", ['class_id' => $this->beta->id])->assertRedirect();

    // A move is never an UPDATE: old row closed, new row opened.
    expect(Enrolment::count())->toBe(2)
        ->and($enrolment->fresh()->status)->toBe(EnrolmentStatus::Moved)
        ->and($enrolment->fresh()->left_at)->not->toBeNull();

    $active = Enrolment::where('status', EnrolmentStatus::Active->value)->first();
    expect($active->class_id)->toBe($this->beta->id);
});

it('refuses to move a student into a full class', function () {
    $tiny = ($this->makeClass)('GAMMA', 1, 'rabu');
    $occupier = Student::factory()->create(['year_level' => 4]);

    $this->post("/jadual/kelas/{$tiny->id}/pelajar", ['student_id' => $occupier->id]);
    $this->post("/jadual/kelas/{$this->alpha->id}/pelajar", ['student_id' => $this->student->id]);

    $enrolment = Enrolment::where('student_id', $this->student->id)->first();

    $this->patch("/enrolan/{$enrolment->id}", ['class_id' => $tiny->id])
        ->assertSessionHas('error', fn (string $m): bool => str_contains($m, 'penuh'));

    expect($enrolment->fresh()->class_id)->toBe($this->alpha->id);
});

it('removes a student from a class without losing the record', function () {
    $this->post("/jadual/kelas/{$this->alpha->id}/pelajar", ['student_id' => $this->student->id]);
    $enrolment = Enrolment::first();

    $this->delete("/enrolan/{$enrolment->id}")->assertRedirect();

    expect(Enrolment::count())->toBe(1)
        ->and($enrolment->fresh()->status)->toBe(EnrolmentStatus::Withdrawn)
        ->and(Enrolment::where('status', EnrolmentStatus::Active->value)->count())->toBe(0);
});

it('pins and unpins a student', function () {
    $this->post("/jadual/kelas/{$this->alpha->id}/pelajar", ['student_id' => $this->student->id]);
    $enrolment = Enrolment::first();

    $this->patch("/enrolan/{$enrolment->id}/semat", ['pinned' => true])->assertRedirect();
    expect($enrolment->fresh()->is_pinned)->toBeTrue();

    $this->patch("/enrolan/{$enrolment->id}/semat", ['pinned' => false]);
    expect($enrolment->fresh()->is_pinned)->toBeFalse();
});

it('carries the pin across a manual move', function () {
    // Unpinning as a side effect of a move would silently hand the student back to
    // the engine, which is the opposite of what pinning is for.
    $this->post("/jadual/kelas/{$this->alpha->id}/pelajar", ['student_id' => $this->student->id]);
    $enrolment = Enrolment::first();
    $this->patch("/enrolan/{$enrolment->id}/semat", ['pinned' => true]);

    $this->patch("/enrolan/{$enrolment->id}", ['class_id' => $this->beta->id]);

    expect(Enrolment::where('status', EnrolmentStatus::Active->value)->first()->is_pinned)->toBeTrue();
});

it('only offers unplaced students of the matching year', function () {
    $placed = Student::factory()->create(['year_level' => 4]);
    Student::factory()->create(['year_level' => 5, 'name' => 'Tahun Lain']);
    $this->post("/jadual/kelas/{$this->beta->id}/pelajar", ['student_id' => $placed->id]);

    $this->get("/jadual/kelas/{$this->alpha->id}")
        ->assertInertia(fn ($page) => $page
            ->has('boleh_tambah', 1)   // only Ahmad remains: right year, no class
            ->where('boleh_tambah.0.nama', 'Ahmad')
            ->etc()
        );
});
