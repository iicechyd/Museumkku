<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tmss;
use App\Models\Activity;

class TmssSeeder extends Seeder
{
    public function run(): void
    {
        $tmss = [
            1 => [
                ['start_time' => '09:30:00', 'end_time' => '10:00:00'],
                ['start_time' => '10:00:00', 'end_time' => '10:30:00'],
                ['start_time' => '10:30:00', 'end_time' => '11:00:00'],
                ['start_time' => '11:00:00', 'end_time' => '11:30:00'],
                ['start_time' => '11:30:00', 'end_time' => '12:00:00'],
                ['start_time' => '13:00:00', 'end_time' => '13:30:00'],
                ['start_time' => '13:30:00', 'end_time' => '14:00:00'],
                ['start_time' => '14:00:00', 'end_time' => '14:30:00'],
                ['start_time' => '14:30:00', 'end_time' => '15:00:00'],
                ['start_time' => '15:00:00', 'end_time' => '15:30:00'],
                ['start_time' => '15:30:00', 'end_time' => '16:00:00'],
            ],
            2 => [
                ['start_time' => '09:30:00', 'end_time' => '10:00:00'],
                ['start_time' => '10:00:00', 'end_time' => '10:30:00'],
                ['start_time' => '10:30:00', 'end_time' => '11:00:00'],
                ['start_time' => '11:00:00', 'end_time' => '11:30:00'],
                ['start_time' => '11:30:00', 'end_time' => '12:00:00'],
                ['start_time' => '13:00:00', 'end_time' => '13:30:00'],
                ['start_time' => '13:30:00', 'end_time' => '14:00:00'],
                ['start_time' => '14:00:00', 'end_time' => '14:30:00'],
                ['start_time' => '14:30:00', 'end_time' => '15:00:00'],
                ['start_time' => '15:00:00', 'end_time' => '15:30:00'],
                ['start_time' => '15:30:00', 'end_time' => '16:00:00'],
            ],
            3 => [
                ['start_time' => '09:30:00', 'end_time' => '10:30:00'],
                ['start_time' => '10:30:00', 'end_time' => '11:30:00'],
                ['start_time' => '13:00:00', 'end_time' => '14:00:00'],
                ['start_time' => '14:00:00', 'end_time' => '15:00:00'],
                ['start_time' => '15:00:00', 'end_time' => '16:00:00'],
            ],
        ];
        foreach ($tmss as $activity_id => $slots) {
            foreach ($slots as $slot) {
                Tmss::create([
                    'activity_id' => $activity_id,
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                ]);
            }
        }

        $this->command->info('สร้างข้อมูล Tmss สำเร็จ');
    }
}
