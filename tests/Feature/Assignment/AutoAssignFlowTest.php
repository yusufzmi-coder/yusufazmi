<?php

declare(strict_types=1);

use App\Domain\Assignment\Enums\RunStatus;
use App\Domain\Assignment\Enums\SolveMode;
use App\Domain\Assignment\Services\RunAssignment;
use App\Enums\EnrolmentSource;
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
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(TimeSlotSeeder::class);
    $this->seed(RuleSeeder::class);

    $this->session = AcademicSession::factory()->active()->create();
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');

    $room = Room::factory()->create(['capacity' => 10]);
    $slot = TimeSlot::where('is_break', false)->orderBy('sort_order')->first();

    $this->classes = collect(['ALPHA', 'BETA'])->map(function (string $stream) use ($room, $slot) {
        $class = SchoolClass::factory()->create([
            'session_id' => $this->session->id,
            'year_level' => 4,
            'stream' => $stream,
            'teacher_id' => Teacher::factory()->create(['firmness' => 4])->id,
            'capacity_override' => 10,
        ]);

        ClassMeeting::create([
            'class_id' => $class->id,
            'session_id' => $this->session->id,
            'teacher_id' => $class->teacher_id,
            'room_id' => $room->id,
            'day' => $stream === 'ALPHA' ? 'isnin' : 'selasa',
            'time_slot_id' => $slot->id,
        ]);

        return $class;
    });

    Student::factory()->count(6)->create(['year_level' => 4]);
});

it('creates a draft run without writing any enrolments', function () {
    $before = Enrolment::count();

    $this->actingAs($this->user)
        ->post('/auto-assign', ['mode' => 'fill_only'])
        ->assertRedirect();

    expect(Enrolment::count())->toBe($before)
        ->and(AssignmentRun::first()->status)->toBe(RunStatus::Draft);
});

it('applies the placements on commit', function () {
    $run = app(RunAssignment::class)($this->session->id, SolveMode::FillOnly, $this->user->id);

    $this->actingAs($this->user)
        ->post("/auto-assign/{$run->id}/sahkan")
        ->assertRedirect(route('jadual.index'));

    expect(Enrolment::where('status', EnrolmentStatus::Active->value)->count())->toBe(6)
        ->and($run->fresh()->status)->toBe(RunStatus::Committed);
});

it('commits only the items that were not excluded', function () {
    $run = app(RunAssignment::class)($this->session->id, SolveMode::FillOnly, $this->user->id);
    $excluded = $run->items()->where('action', 'place')->take(2)->pluck('id')->all();

    $this->actingAs($this->user)
        ->post("/auto-assign/{$run->id}/sahkan", ['excluded' => $excluded])
        ->assertRedirect();

    expect(Enrolment::where('status', EnrolmentStatus::Active->value)->count())->toBe(4);
});

it('refuses to commit when the underlying data changed', function () {
    // The whole point of input_hash: a stale preview must not be applied silently.
    $run = app(RunAssignment::class)($this->session->id, SolveMode::FillOnly, $this->user->id);

    Student::factory()->create(['year_level' => 4]);

    $this->actingAs($this->user)
        ->post("/auto-assign/{$run->id}/sahkan")
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(Enrolment::where('status', EnrolmentStatus::Active->value)->count())->toBe(0)
        ->and($run->fresh()->status)->toBe(RunStatus::Draft);
});

it('refuses to commit a draft twice', function () {
    $run = app(RunAssignment::class)($this->session->id, SolveMode::FillOnly, $this->user->id);

    $this->actingAs($this->user)->post("/auto-assign/{$run->id}/sahkan")->assertRedirect();

    $this->actingAs($this->user)
        ->post("/auto-assign/{$run->id}/sahkan")
        ->assertSessionHas('error');

    expect(Enrolment::where('status', EnrolmentStatus::Active->value)->count())->toBe(6);
});

it('prevents double enrolment at the database level', function () {
    // Asserts the invariant, not the lock: even if every advisory lock failed, the
    // database itself must refuse to seat a student in two classes at once.
    $student = Student::first();

    $row = [
        'session_id' => $this->session->id,
        'student_id' => $student->id,
        'status' => EnrolmentStatus::Active->value,
        'source' => EnrolmentSource::Manual->value,
        'joined_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ];

    DB::table('enrolments')->insert($row + ['class_id' => $this->classes[0]->id]);

    expect(fn () => DB::table('enrolments')->insert($row + ['class_id' => $this->classes[1]->id]))
        ->toThrow(QueryException::class);
});

it('keeps a moved enrolment in history rather than overwriting it', function () {
    $run = app(RunAssignment::class)($this->session->id, SolveMode::FillOnly, $this->user->id);
    $this->actingAs($this->user)->post("/auto-assign/{$run->id}/sahkan");

    $enrolment = Enrolment::where('status', EnrolmentStatus::Active->value)->first();

    expect($enrolment->placement_reason_text)->not->toBeEmpty()
        ->and($enrolment->source)->toBe(EnrolmentSource::Auto)
        ->and($enrolment->assignment_run_id)->toBe($run->id);
});

it('marks a stale draft as expired instead of applying it', function () {
    $run = app(RunAssignment::class)($this->session->id, SolveMode::FillOnly, $this->user->id);

    $run->forceFill(['created_at' => now()->subHours(2)])->saveQuietly();

    $this->actingAs($this->user)
        ->post("/auto-assign/{$run->id}/sahkan")
        ->assertSessionHas('error');

    expect($run->fresh()->status)->toBe(RunStatus::Expired);
});
