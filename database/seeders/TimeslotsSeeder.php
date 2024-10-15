<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Timeslots;

class TimeslotsSeeder extends Seeder
{
    public function run(): void
    {
        Timeslots::create([
            'activity_id' => 1, //พิพิธภัณฑ์ธรรมชาติวิทยา
            'start_time' => '09:30:00',
            'end_time' => '10:00:00',
        ]);
        Timeslots::create([
            'activity_id' => 1,
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
        ]);
        Timeslots::create([
            'activity_id' => 1,
            'start_time' => '10:30:00',
            'end_time' => '11:00:00',
        ]);
        Timeslots::create([
            'activity_id' => 1,
            'start_time' => '11:00:00',
            'end_time' => '11:30:00',
        ]);
        Timeslots::create([
            'activity_id' => 1,
            'start_time' => '11:30:00',
            'end_time' => '12:00:00',
        ]);
        Timeslots::create([
            'activity_id' => 1,
            'start_time' => '13:00:00',
            'end_time' => '13:30:00',
        ]);
        Timeslots::create([
            'activity_id' => 1,
            'start_time' => '13:30:00',
            'end_time' => '14:00:00',
        ]);
        Timeslots::create([
            'activity_id' => 1,
            'start_time' => '14:00:00',
            'end_time' => '14:30:00',
        ]);
        Timeslots::create([
            'activity_id' => 1,
            'start_time' => '14:30:00',
            'end_time' => '15:00:00',
        ]);
        Timeslots::create([
            'activity_id' => 1,
            'start_time' => '15:00:00',
            'end_time' => '15:30:00',
        ]);
        Timeslots::create([
            'activity_id' => 1,
            'start_time' => '15:30:00',
            'end_time' => '16:00:00',
        ]);
        Timeslots::create([
            'activity_id' => 1,
            'start_time' => '16:00:00',
            'end_time' => '16:30:00',
        ]);
        Timeslots::create([
            'activity_id' => 2, //พิพิธภัณฑ์วิทยาศาสตร์
            'start_time' => '09:30:00',
            'end_time' => '10:00:00',
        ]);
        Timeslots::create([
            'activity_id' => 2,
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
        ]);
        Timeslots::create([
            'activity_id' => 2,
            'start_time' => '10:30:00',
            'end_time' => '11:00:00',
        ]);
        Timeslots::create([
            'activity_id' => 2,
            'start_time' => '11:00:00',
            'end_time' => '11:30:00',
        ]);
        Timeslots::create([
            'activity_id' => 2,
            'start_time' => '11:30:00',
            'end_time' => '12:00:00',
        ]);
        Timeslots::create([
            'activity_id' => 2,
            'start_time' => '13:00:00',
            'end_time' => '13:30:00',
        ]);
        Timeslots::create([
            'activity_id' => 2,
            'start_time' => '13:30:00',
            'end_time' => '14:00:00',
        ]);
        Timeslots::create([
            'activity_id' => 2,
            'start_time' => '14:00:00',
            'end_time' => '14:30:00',
        ]);
        Timeslots::create([
            'activity_id' => 2,
            'start_time' => '14:30:00',
            'end_time' => '15:00:00',
        ]);
        Timeslots::create([
            'activity_id' => 2,
            'start_time' => '15:00:00',
            'end_time' => '15:30:00',
        ]);
        Timeslots::create([
            'activity_id' => 2,
            'start_time' => '15:30:00',
            'end_time' => '16:00:00',
        ]);
        Timeslots::create([
            'activity_id' => 2,
            'start_time' => '16:00:00',
            'end_time' => '16:30:00',
        ]);
        Timeslots::create([
            'activity_id' => 3,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ]);
    }
}
