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

    $this->class = SchoolClass::factory()->create([
        'session_id' => $this->session->id,
        'year_level' => 4,
        'stream' => 'ALPHA',
        'teacher_id' => Teacher::factory()->create(['name' => 'Cikgu Farah'])->id,
        'capacity_override' => 30,
    ]);

    ClassMeeting::create([
        'class_id' => $this->class->id,
        'session_id' => $this->session->id,
        'teacher_id' => $this->class->teacher_id,
        'room_id' => Room::factory()->create(['name' => 'Bilik 1', 'capacity' => 30])->id,
        'day' => 'isnin',
        'time_slot_id' => TimeSlot::where('is_break', false)->orderBy('sort_order')->first()->id,
    ]);

    $this->student = Student::factory()->create([
        'year_level' => 4,
        'name' => 'Nurul Aisyah',
        'student_code' => 'P-0001',
    ]);

    Enrolment::create([
        'session_id' => $this->session->id,
        'student_id' => $this->student->id,
        'class_id' => $this->class->id,
        'status' => 'active',
        'source' => 'manual',
        'joined_at' => now(),
    ]);
});

it('exports the weekly timetable as a PDF', function () {
    $response = $this->get('/eksport/jadual.pdf');

    $response->assertOk()->assertHeader('Content-Type', 'application/pdf');

    expect($response->getContent())->toStartWith('%PDF');
});

it('exports a class roster as a PDF', function () {
    $response = $this->get("/eksport/kelas/{$this->class->id}.pdf");

    $response->assertOk()->assertHeader('Content-Type', 'application/pdf');

    expect($response->headers->get('Content-Disposition'))->toContain('senarai-4 ALPHA.pdf');
});

it('exports students as CSV with headers the importer understands', function () {
    // Round-tripping matters: an admin should be able to export, fix in a
    // spreadsheet, and import straight back.
    $csv = $this->get('/eksport/pelajar.csv')
        ->assertOk()
        ->streamedContent();

    $lines = array_values(array_filter(explode("\n", trim($csv))));
    $header = str_getcsv(ltrim($lines[0], "\xEF\xBB\xBF"));

    expect($header)->toContain('Kod', 'Nama', 'Jantina', 'Tahun', 'Keluarga')
        ->and($lines[1])->toContain('P-0001')
        ->and($lines[1])->toContain('Nurul Aisyah')
        ->and($lines[1])->toContain('4 ALPHA');
});

it('writes a BOM so the CSV opens correctly in Excel', function () {
    $csv = $this->get('/eksport/pelajar.csv')->streamedContent();

    expect(substr($csv, 0, 3))->toBe("\xEF\xBB\xBF");
});

it('exports class statistics as CSV', function () {
    $csv = $this->get('/eksport/statistik.csv')->assertOk()->streamedContent();

    expect($csv)->toContain('Kelas', 'Kapasiti', 'Penggunaan')
        ->and($csv)->toContain('4 ALPHA')
        ->and($csv)->toContain('Cikgu Farah');
});

it('keeps exports behind authentication', function () {
    auth()->logout();

    foreach (['/eksport/jadual.pdf', '/eksport/pelajar.csv', '/eksport/statistik.csv'] as $url) {
        $this->get($url)->assertRedirect(route('login'));
    }
});
