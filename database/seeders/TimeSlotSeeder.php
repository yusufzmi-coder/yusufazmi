<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    /** The grid rows, exactly as they appear on the timetable screen. */
    private const SLOTS = [
        ['8:00 AM - 9:00 AM', '08:00:00', '09:00:00', false, 1],
        ['9:00 AM - 10:00 AM', '09:00:00', '10:00:00', false, 2],
        ['10:00 AM - 11:00 AM', '10:00:00', '11:00:00', false, 3],
        ['REHAT', '11:00:00', '12:00:00', true, 4],
        ['2:00 PM - 3:00 PM', '14:00:00', '15:00:00', false, 5],
        ['3:00 PM - 4:00 PM', '15:00:00', '16:00:00', false, 6],
        ['4:00 PM - 5:00 PM', '16:00:00', '17:00:00', false, 7],
    ];

    public function run(): void
    {
        foreach (self::SLOTS as [$label, $start, $end, $isBreak, $order]) {
            TimeSlot::updateOrCreate(
                ['sort_order' => $order],
                ['label' => $label, 'starts_at' => $start, 'ends_at' => $end, 'is_break' => $isBreak],
            );
        }
    }
}
