<?php

declare(strict_types=1);

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

    $this->teacher = Teacher::factory()->create(['name' => 'Cikgu Farah']);
    $this->room = Room::factory()->create(['name' => 'Bilik 1', 'capacity' => 30]);
    $this->slot = TimeSlot::where('is_break', false)->orderBy('sort_order')->first();
    $this->break = TimeSlot::where('is_break', true)->first();
});

function classPayload(array $overrides = []): array
{
    return array_merge([
        'year_level' => 4,
        'stream' => 'ALPHA',
        'teacher_id' => test()->teacher->id,
        'is_active' => true,
        'meetings' => [
            ['day' => 'isnin', 'time_slot_id' => test()->slot->id, 'room_id' => test()->room->id],
        ],
    ], $overrides);
}

it('creates a class with its weekly meetings', function () {
    $this->post('/kelas', classPayload([
        'meetings' => [
            ['day' => 'isnin', 'time_slot_id' => $this->slot->id, 'room_id' => $this->room->id],
            ['day' => 'khamis', 'time_slot_id' => $this->slot->id, 'room_id' => $this->room->id],
        ],
    ]))->assertRedirect(route('kelas.index'));

    $class = SchoolClass::first();

    // name is a generated column, so it must come back derived rather than written.
    expect($class->name)->toBe('4 ALPHA')
        ->and($class->meetings)->toHaveCount(2);
});

it('refuses to double-book a room and names the clash', function () {
    $this->post('/kelas', classPayload());

    $this->post('/kelas', classPayload(['stream' => 'BETA']))
        ->assertRedirect()
        ->assertSessionHas('error', fn (string $m): bool => str_contains($m, 'Bilik 1')
            && str_contains($m, '4 ALPHA'));

    expect(SchoolClass::count())->toBe(1);
});

it('refuses to double-book a teacher and names the clash', function () {
    $other = Room::factory()->create(['name' => 'Bilik 2']);

    $this->post('/kelas', classPayload());

    $this->post('/kelas', classPayload([
        'stream' => 'BETA',
        'meetings' => [['day' => 'isnin', 'time_slot_id' => $this->slot->id, 'room_id' => $other->id]],
    ]))
        ->assertRedirect()
        ->assertSessionHas('error', fn (string $m): bool => str_contains($m, 'Cikgu Farah'));

    expect(SchoolClass::count())->toBe(1);
});

it('refuses a class scheduled into the REHAT row', function () {
    $this->post('/kelas', classPayload([
        'meetings' => [['day' => 'isnin', 'time_slot_id' => $this->break->id, 'room_id' => $this->room->id]],
    ]))->assertSessionHasErrors('meetings.0.time_slot_id');
});

it('refuses a duplicate year and stream', function () {
    $this->post('/kelas', classPayload());

    $this->post('/kelas', classPayload([
        'meetings' => [['day' => 'selasa', 'time_slot_id' => $this->slot->id, 'room_id' => $this->room->id]],
    ]))->assertSessionHasErrors('session_year_stream');
});

it('refuses the same slot twice within one class', function () {
    $this->post('/kelas', classPayload([
        'meetings' => [
            ['day' => 'isnin', 'time_slot_id' => $this->slot->id, 'room_id' => $this->room->id],
            ['day' => 'isnin', 'time_slot_id' => $this->slot->id, 'room_id' => $this->room->id],
        ],
    ]))->assertSessionHasErrors('meetings.1.day');
});

it('keeps the clash index truthful when a class changes teacher', function () {
    // teacher_id is denormalised onto class_meetings so the partial unique index can
    // exist at all; if the copy drifts, clash detection silently stops working.
    $this->post('/kelas', classPayload());
    $class = SchoolClass::first();

    $newTeacher = Teacher::factory()->create(['name' => 'Cikgu Amir']);

    $this->put("/kelas/{$class->id}", classPayload(['teacher_id' => $newTeacher->id]))
        ->assertRedirect(route('kelas.index'));

    expect(ClassMeeting::first()->teacher_id)->toBe($newTeacher->id);
});

it('refuses to delete a class that still holds students', function () {
    $this->post('/kelas', classPayload());
    $class = SchoolClass::first();

    Enrolment::create([
        'session_id' => $this->session->id,
        'student_id' => Student::factory()->create(['year_level' => 4])->id,
        'class_id' => $class->id,
        'status' => 'active',
        'source' => 'manual',
        'joined_at' => now(),
    ]);

    $this->delete("/kelas/{$class->id}")->assertSessionHas('error');

    expect(SchoolClass::count())->toBe(1);
});

it('deletes an empty class along with its meetings', function () {
    $this->post('/kelas', classPayload());
    $class = SchoolClass::first();

    $this->delete("/kelas/{$class->id}")->assertRedirect(route('kelas.index'));

    expect(SchoolClass::count())->toBe(0)
        ->and(ClassMeeting::count())->toBe(0);
});
