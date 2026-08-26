<?php

declare(strict_types=1);

use App\Domain\Assignment\Enums\RunStatus;
use App\Domain\Assignment\Enums\SolveMode;
use App\Domain\Assignment\Services\RunAssignment;
use App\Enums\EnrolmentStatus;
use App\Models\AcademicSession;
use App\Models\AssignmentRun;
use App\Models\ClassMeeting;
use App\Models\Enrolment;
use App\Models\Room;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TimeSlot;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\RuleSeeder;
use Database\Seeders\TimeSlotSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(TimeSlotSeeder::class);
    $this->seed(RuleSeeder::class);

    $this->session = AcademicSession::factory()->active()->create();
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');
    $this->actingAs($this->user);

    $slot = TimeSlot::where('is_break', false)->orderBy('sort_order')->first();

    foreach (['ALPHA' => 'isnin', 'BETA' => 'selasa'] as $stream => $day) {
        $class = SchoolClass::factory()->create([
            'session_id' => $this->session->id,
            'year_level' => 4,
            'stream' => $stream,
            'teacher_id' => Teacher::factory()->create()->id,
            'capacity_override' => 10,
        ]);

        ClassMeeting::create([
            'class_id' => $class->id,
            'session_id' => $this->session->id,
            'teacher_id' => $class->teacher_id,
            'room_id' => Room::factory()->create(['capacity' => 10])->id,
            'day' => $day,
            'time_slot_id' => $slot->id,
        ]);
    }

    Student::factory()->count(4)->create(['year_level' => 4]);

    $this->runAndCommit = function (SolveMode $mode = SolveMode::FillOnly): AssignmentRun {
        $run = app(RunAssignment::class)($this->session->id, $mode, $this->user->id);
        $this->post("/auto-assign/{$run->id}/sahkan");

        return $run->fresh();
    };
});

it('undoes a committed run and leaves nobody placed', function () {
    $run = ($this->runAndCommit)();

    expect(Enrolment::where('status', EnrolmentStatus::Active->value)->count())->toBe(4);

    $this->post("/auto-assign/{$run->id}/buat-asal")->assertRedirect(route('auto-assign.index'));

    expect(Enrolment::where('status', EnrolmentStatus::Active->value)->count())->toBe(0)
        ->and($run->fresh()->status)->toBe(RunStatus::Reverted);
});

it('restores the previous placement when undoing a rebalance', function () {
    ($this->runAndCommit)();
    $before = Enrolment::where('status', EnrolmentStatus::Active->value)
        ->pluck('class_id', 'student_id');

    // Force a move: pushing everyone into one class means rebalance has work to do.
    $alpha = SchoolClass::where('stream', 'ALPHA')->first();
    Enrolment::where('status', EnrolmentStatus::Active->value)->update(['class_id' => $alpha->id]);

    $second = ($this->runAndCommit)(SolveMode::Rebalance);

    $this->post("/auto-assign/{$second->id}/buat-asal")->assertRedirect();

    $after = Enrolment::where('status', EnrolmentStatus::Active->value)
        ->pluck('class_id', 'student_id');

    // Every student is back in the class they occupied before the second run.
    expect($after->count())->toBe(4)
        ->and($after->every(fn (int $classId): bool => $classId === $alpha->id))->toBeTrue()
        ->and($before->count())->toBe(4);
});

it('refuses to undo anything but the latest committed run', function () {
    // Rolling back an older run would mean unpicking everything layered on top,
    // and there is no honest answer to which placement should come back.
    $first = ($this->runAndCommit)();
    Student::factory()->create(['year_level' => 4]);
    ($this->runAndCommit)();

    $this->post("/auto-assign/{$first->id}/buat-asal")
        ->assertSessionHas('error', fn (string $m): bool => str_contains($m, 'lebih baharu'));

    expect($first->fresh()->status)->toBe(RunStatus::Committed);
});

it('refuses to undo a run that was never committed', function () {
    $draft = app(RunAssignment::class)($this->session->id, SolveMode::FillOnly, $this->user->id);

    $this->post("/auto-assign/{$draft->id}/buat-asal")->assertSessionHas('error');

    expect($draft->fresh()->status)->toBe(RunStatus::Draft);
});

it('leaves a manually changed placement alone when undoing', function () {
    // An admin's deliberate move outranks an automated rollback.
    $run = ($this->runAndCommit)();

    $enrolment = Enrolment::where('status', EnrolmentStatus::Active->value)->first();
    $this->delete("/enrolan/{$enrolment->id}");

    $this->post("/auto-assign/{$run->id}/buat-asal")->assertRedirect();

    expect($enrolment->fresh()->status)->toBe(EnrolmentStatus::Withdrawn);
});

it('offers undo only for the newest committed run', function () {
    $first = ($this->runAndCommit)();
    Student::factory()->create(['year_level' => 4]);
    $second = ($this->runAndCommit)();

    $this->get('/auto-assign')->assertInertia(function ($page) use ($first, $second) {
        $runs = collect($page->toArray()['props']['runs'])->keyBy('id');

        expect($runs[$second->id]['boleh_buat_asal'])->toBeTrue()
            ->and($runs[$first->id]['boleh_buat_asal'])->toBeFalse();
    });
});
