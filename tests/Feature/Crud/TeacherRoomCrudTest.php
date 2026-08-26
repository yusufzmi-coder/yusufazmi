<?php

declare(strict_types=1);

use App\Models\AcademicSession;
use App\Models\ClassMeeting;
use App\Models\Room;
use App\Models\SchoolClass;
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
});

it('creates a teacher with a firmness level', function () {
    $this->post('/guru', [
        'name' => 'Cikgu Farah',
        'firmness' => 5,
        'max_classes' => 6,
        'is_active' => true,
    ])->assertRedirect(route('guru.index'));

    expect(Teacher::first()->firmnessLabel())->toBe('Sangat Tegas');
});

it('rejects a firmness outside 1 to 5', function () {
    $this->post('/guru', ['name' => 'X', 'firmness' => 9, 'max_classes' => 6])
        ->assertSessionHasErrors('firmness');
});

it('refuses to delete a teacher who still has classes', function () {
    // These are soft deletes, so the foreign key never complains. The guard has to be
    // explicit or the teacher silently disappears while classes still reference them.
    $teacher = Teacher::factory()->create(['name' => 'Cikgu Farah']);

    SchoolClass::factory()->create([
        'session_id' => $this->session->id,
        'teacher_id' => $teacher->id,
        'year_level' => 4,
        'stream' => 'ALPHA',
    ]);

    $this->delete("/guru/{$teacher->id}")->assertSessionHas('error');

    expect(Teacher::whereKey($teacher->id)->exists())->toBeTrue();
});

it('deletes a teacher with no classes', function () {
    $teacher = Teacher::factory()->create();

    $this->delete("/guru/{$teacher->id}")->assertRedirect(route('guru.index'));

    expect(Teacher::whereKey($teacher->id)->exists())->toBeFalse();
});

it('creates a room', function () {
    $this->post('/bilik', ['name' => 'Bilik 1', 'capacity' => 25, 'is_active' => true])
        ->assertRedirect(route('bilik.index'));

    expect(Room::first()->capacity)->toBe(25);
});

it('refuses a duplicate room name', function () {
    Room::factory()->create(['name' => 'Bilik 1']);

    $this->post('/bilik', ['name' => 'Bilik 1', 'capacity' => 30])
        ->assertSessionHasErrors('name');
});

it('refuses to delete a room still used in the timetable', function () {
    $room = Room::factory()->create(['name' => 'Bilik 1']);
    $class = SchoolClass::factory()->create([
        'session_id' => $this->session->id,
        'teacher_id' => Teacher::factory()->create()->id,
        'year_level' => 4,
    ]);

    ClassMeeting::create([
        'class_id' => $class->id,
        'session_id' => $this->session->id,
        'teacher_id' => $class->teacher_id,
        'room_id' => $room->id,
        'day' => 'isnin',
        'time_slot_id' => TimeSlot::where('is_break', false)->first()->id,
    ]);

    $this->delete("/bilik/{$room->id}")->assertSessionHas('error');

    expect(Room::whereKey($room->id)->exists())->toBeTrue();
});

it('deletes an unused room', function () {
    $room = Room::factory()->create();

    $this->delete("/bilik/{$room->id}")->assertRedirect(route('bilik.index'));

    expect(Room::whereKey($room->id)->exists())->toBeFalse();
});
