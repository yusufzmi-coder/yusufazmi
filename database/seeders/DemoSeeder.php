<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BehaviourLevel;
use App\Enums\Day;
use App\Enums\Gender;
use App\Enums\SiblingPolicy;
use App\Models\AcademicSession;
use App\Models\ClassMeeting;
use App\Models\Family;
use App\Models\Guardian;
use App\Models\Room;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TimeSlot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Realistic demo data. Students are deliberately left UNASSIGNED: the first thing
 * the admin does is press "Jalankan Auto Assign", which is the whole point of the
 * system and makes the demo mean something.
 */
class DemoSeeder extends Seeder
{
    private const TEACHER_NAMES = [
        ['Cikgu Farah', 5], ['Cikgu Amir', 4], ['Cikgu Lisa', 3], ['Cikgu Rahim', 5],
        ['Cikgu Siti', 2], ['Cikgu Zainal', 4], ['Cikgu Noraini', 3], ['Cikgu Hafiz', 2],
        ['Cikgu Mei Ling', 4], ['Cikgu Kumar', 3], ['Cikgu Aisyah', 5], ['Cikgu Danial', 2],
    ];

    private const STREAMS = ['ALPHA', 'BETA', 'GAMMA'];

    /** Years that actually run classes. Year 1 exists but has no timetable yet. */
    private const TEACHING_YEARS = [2, 3, 4, 5, 6];

    public function run(): void
    {
        $session = AcademicSession::active();

        $teachers = collect(self::TEACHER_NAMES)->map(fn (array $t): Teacher => Teacher::create([
            'name' => $t[0],
            'firmness' => $t[1],
            'phone' => '+6012'.random_int(1000000, 9999999),
            'email' => str($t[0])->after('Cikgu ')->lower()->append('@jadual.test')->value(),
            'is_active' => true,
        ]));

        $rooms = collect(range(1, 9))->map(fn (int $n): Room => Room::create([
            'name' => "Bilik {$n}",
            'capacity' => 30,
            'location' => 'Aras '.(int) ceil($n / 3),
            'is_active' => true,
        ]));

        $classes = $this->createClasses($session->id, $teachers);
        $this->scheduleMeetings($session->id, $classes, $rooms);
        $this->createStudents();

        $this->command->info(sprintf(
            'Demo: %d pelajar, %d kelas, %d guru, %d bilik.',
            Student::count(), SchoolClass::count(), $teachers->count(), $rooms->count(),
        ));
    }

    /** 18 classes: years 1-6 x 3 streams. Year 1's three have no slots yet ("Kosong: 3"). */
    private function createClasses(int $sessionId, $teachers): Collection
    {
        $classes = collect();
        $i = 0;

        foreach (range(1, 6) as $year) {
            foreach (self::STREAMS as $stream) {
                $classes->push(SchoolClass::create([
                    'session_id' => $sessionId,
                    'year_level' => $year,
                    'stream' => $stream,
                    'teacher_id' => $teachers[$i++ % $teachers->count()]->id,
                    'capacity_override' => null,
                    'is_active' => true,
                ]));
            }
        }

        return $classes;
    }

    /**
     * One class per grid cell, which is exactly what the timetable screen shows and
     * makes teacher and room clashes impossible by construction.
     */
    /**
     * @param  Collection<int, SchoolClass>  $classes
     * @param  Collection<int, Room>  $rooms
     */
    private function scheduleMeetings(int $sessionId, Collection $classes, Collection $rooms): void
    {
        $slots = TimeSlot::where('is_break', false)->orderBy('sort_order')->get();
        $scheduled = $classes->filter(fn (SchoolClass $c): bool => in_array($c->year_level, self::TEACHING_YEARS, true))->values();

        $cells = [];
        foreach (Day::schoolWeek() as $day) {
            foreach ($slots as $slot) {
                $cells[] = [$day, $slot];
            }
        }

        // 15 classes x 2 meetings = 30 cells; leave the last two empty so the grid
        // shows a couple of "KOSONG / Slot tersedia" cells like the real thing.
        $meetings = [];
        foreach ($scheduled as $index => $class) {
            $meetings[] = [$class, $index];
            $meetings[] = [$class, $index + $scheduled->count()];
        }

        usort($meetings, static fn (array $a, array $b): int => $a[1] <=> $b[1]);

        foreach ($meetings as $position => [$class, $cellIndex]) {
            if ($cellIndex >= count($cells) - 2) {
                continue;   // leave KOSONG
            }

            [$day, $slot] = $cells[$cellIndex];

            ClassMeeting::create([
                'class_id' => $class->id,
                'session_id' => $sessionId,
                'teacher_id' => $class->teacher_id,
                'room_id' => $rooms[$position % $rooms->count()]->id,
                'day' => $day->value,
                'time_slot_id' => $slot->id,
            ]);
        }
    }

    /**
     * 236 students, 228 of them active. Around a third belong to families with
     * siblings, and a handful of those families explicitly want their children kept
     * together -- so the sibling rule has something real to chew on.
     */
    private function createStudents(): void
    {
        $target = 236;
        $created = 0;
        $code = 1;

        // Families with 2-3 siblings.
        $familyCount = 34;

        for ($f = 1; $f <= $familyCount && $created < $target; $f++) {
            $family = Family::create([
                'name' => 'Keluarga '.MalayNames::surname($f),
                // Roughly one family in five asks for their children to stay together.
                'sibling_policy' => $f % 5 === 0 ? SiblingPolicy::Together : SiblingPolicy::Inherit,
                'address' => fake()->address(),
            ]);

            Guardian::create([
                'family_id' => $family->id,
                'name' => 'Encik '.MalayNames::surname($f + 7),
                'phone' => '+6013'.random_int(1000000, 9999999),
                'email' => fake()->unique()->safeEmail(),
                'occupation' => fake()->jobTitle(),
            ]);

            foreach (range(1, random_int(2, 3)) as $ignored) {
                if ($created >= $target) {
                    break;
                }

                $this->makeStudent($code++, $family->id);
                $created++;
            }
        }

        while ($created < $target) {
            $this->makeStudent($code++, null);
            $created++;
        }

        // Eight inactive students, matching the "Tidak Aktif: 8" tile.
        Student::query()->orderByDesc('id')->limit(8)->update(['is_active' => false]);
    }

    private function makeStudent(int $code, ?int $familyId): void
    {
        $gender = random_int(0, 1) === 1 ? Gender::Lelaki : Gender::Perempuan;

        // ~5% of students carry a behaviour flag, a couple of them serious.
        $roll = random_int(1, 100);
        $behaviour = match (true) {
            $roll <= 2 => BehaviourLevel::Kritikal,
            $roll <= 6 => BehaviourLevel::Bermasalah,
            $roll <= 12 => BehaviourLevel::PerluPerhatian,
            default => BehaviourLevel::Normal,
        };

        Student::create([
            'family_id' => $familyId,
            'student_code' => 'P-'.str_pad((string) $code, 4, '0', STR_PAD_LEFT),
            'name' => $gender === Gender::Lelaki
                ? MalayNames::male($code)
                : MalayNames::female($code),
            'gender' => $gender,
            'date_of_birth' => fake()->dateTimeBetween('-13 years', '-7 years'),
            'year_level' => self::TEACHING_YEARS[array_rand(self::TEACHING_YEARS)],
            'behaviour_level' => $behaviour,
            'is_active' => true,
            'enrolled_on' => now()->subMonths(random_int(0, 24)),
        ]);
    }
}
