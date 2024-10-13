<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ActivityType;

class ActivityTypeSeeder extends Seeder
{

    public function run(): void
    {
        $activityTypes = [
            ['type_name' => 'จองเข้าชมพิพิธภัณฑ์'],
            ['type_name' => 'กิจกรรมพิเศษ'],
        ];

        foreach ($activityTypes as $type) {
            ActivityType::create($type);
        }
    }
}
