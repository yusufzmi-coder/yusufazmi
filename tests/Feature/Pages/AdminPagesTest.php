<?php

declare(strict_types=1);

use App\Models\AcademicSession;
use App\Models\ClassMeeting;
use App\Models\Room;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TimeSlot;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\RuleSeeder;
use Database\Seeders\TimeSlotSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(TimeSlotSeeder::class);
    $this->seed(RuleSeeder::class);

    $this->session = AcademicSession::factory()->active()->create();
    $this->user = User::factory()->create();
    $this->user->assignRole('Super Admin');

    $this->class = SchoolClass::factory()->create([
        'session_id' => $this->session->id,
        'year_level' => 4,
        'stream' => 'ALPHA',
        'teacher_id' => Teacher::factory()->create(['name' => 'Cikgu Farah', 'firmness' => 5])->id,
    ]);

    ClassMeeting::create([
        'class_id' => $this->class->id,
        'session_id' => $this->session->id,
        'teacher_id' => $this->class->teacher_id,
        'room_id' => Room::factory()->create(['name' => 'Bilik 1', 'capacity' => 30])->id,
        'day' => 'isnin',
        'time_slot_id' => TimeSlot::where('is_break', false)->orderBy('sort_order')->first()->id,
    ]);

    Student::factory()->count(3)->create(['year_level' => 4]);
});

it('shows the dashboard with live counts and the weekly grid', function () {
    $this->actingAs($this->user)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('dashboard')
            ->where('stats.pelajar.jumlah', 3)
            ->where('stats.kelas.jumlah', 1)
            ->has('grid.days', 5)
            ->has('grid.rows', 7)          // six teaching slots plus REHAT
            ->has('rules', 4)
            ->has('distribution')
        );
});

it('marks the break row so the grid can render REHAT', function () {
    $this->actingAs($this->user)
        ->get('/jadual')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('jadual/index')
            ->where('grid.rows.3.is_break', true)
            ->where('grid.rows.0.cells.0.kelas.nama', '4 ALPHA')
            ->where('grid.rows.0.cells.0.kelas.guru', 'Cikgu Farah')
            ->where('grid.rows.0.cells.1.kelas', null)
            ->etc()
        );
});

it('lists rules alongside the constraints that cannot be switched off', function () {
    $this->actingAs($this->user)
        ->get('/peraturan')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('rules/index')
            ->has('rules', 4)
            // Year match and room capacity are physical facts, shown but not toggleable.
            ->has('always_on', 2)
        );
});

it('filters students down to those without a class', function () {
    $this->actingAs($this->user)
        ->get('/pelajar?status=belum_ada_kelas')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('pelajar/index')
            ->where('pelajar.total', 3)
            ->etc()
        );
});

it('shows a class roster', function () {
    $this->actingAs($this->user)
        ->get("/jadual/kelas/{$this->class->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('jadual/kelas')
            ->where('kelas.nama', '4 ALPHA')
            ->has('kelas.pertemuan', 1)
            ->has('pelajar', 0)
        );
});

it('keeps every admin page behind authentication', function () {
    foreach (['/dashboard', '/jadual', '/pelajar', '/peraturan', '/auto-assign'] as $url) {
        $this->get($url)->assertRedirect(route('login'));
    }
});
