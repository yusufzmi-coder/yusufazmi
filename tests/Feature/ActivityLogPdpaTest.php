<?php

declare(strict_types=1);

use App\Enums\BehaviourLevel;
use App\Models\Student;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

it('records what changed on a student', function () {
    $student = Student::factory()->create(['behaviour_level' => BehaviourLevel::Normal]);

    $student->update(['behaviour_level' => BehaviourLevel::Bermasalah]);

    $activity = Activity::latest('id')->first();

    expect($activity->description)->toBe('Pelajar dikemas kini')
        ->and($activity->attribute_changes['attributes']['behaviour_level'])->toBe(2)
        ->and($activity->attribute_changes['old']['behaviour_level'])->toBe(0);
});

it('never writes PII into the audit trail', function () {
    // The allow-list is the whole defence here. Without it the activity log quietly
    // becomes an unencrypted shadow copy of the most sensitive columns in the system.
    $student = Student::factory()->create();

    $student->update([
        'name' => 'Nama Baharu',
        'national_id' => '070101-14-5555',
        'date_of_birth' => '2007-01-01',
        'phone' => '+60123456789',
        'special_needs' => 'Maklumat perubatan sulit',
    ]);

    $logged = Activity::latest('id')->first()->attribute_changes;
    $fields = array_keys($logged['attributes'] ?? []);

    expect($fields)->not->toContain('national_id')
        ->and($fields)->not->toContain('date_of_birth')
        ->and($fields)->not->toContain('phone')
        ->and($fields)->not->toContain('special_needs');

    $raw = json_encode($logged);

    expect($raw)->not->toContain('070101-14-5555')
        ->and($raw)->not->toContain('Maklumat perubatan sulit')
        ->and($raw)->not->toContain('+60123456789');
});

it('attributes the change to the signed-in admin', function () {
    $admin = User::factory()->create();
    $this->actingAs($admin);

    Student::factory()->create()->update(['year_level' => 5]);

    expect(Activity::latest('id')->first()->causer_id)->toBe($admin->id);
});
