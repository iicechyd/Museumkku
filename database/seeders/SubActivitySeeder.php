<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubActivity;

class SubActivitySeeder extends Seeder
{
    public function run(): void
    {
        $subactivitys = [
            4 => [
                ['sub_activity_name' => 'เครื่องในสัตว์โลกมหัศจรรย์'],
                ['sub_activity_name' => 'สานฝันนักธรณี'],
                ['sub_activity_name' => 'เอาชีวิตรอดในอาณาจักรแมลง'],
                ['sub_activity_name' => 'Robotics Camp'],
                ['sub_activity_name' => 'ถอดรหัสพันธุกรรม'],
                ['sub_activity_name' => 'นักสำรวจแห่งพงไพร'],
                ['sub_activity_name' => 'ค้นฟ้าคว้าดาว'],
                ['sub_activity_name' => 'ส่องฟ้าหานก'],
            ],
            5 => [
                ['sub_activity_name' => 'Aniaml Anatomy'],
                ['sub_activity_name' => 'ตะลุยโลกธรณี'],
                ['sub_activity_name' => 'บุกอาณาจักรแมลง'],
                ['sub_activity_name' => 'ท่องโลกหุ่นยนต์'],
                ['sub_activity_name' => 'DNA กุญแจของสิ่งมีชีวิต'],
                ['sub_activity_name' => 'เดินป่าศึกษาธรรมชาติ'],
                ['sub_activity_name' => 'ไขความลับของท้องฟ้าและดวงดาว'],
                ['sub_activity_name' => 'ตามล่าหาสัตว์ปีกแหล่งโลกดึกดำบรรพ์ (ดูนก)'],
            ],
        ];
        foreach ($subactivitys as $activity_id => $slots) {
            foreach ($slots as $slot) {
                SubActivity::create([
                    'activity_id' => $activity_id,
                    'sub_activity_name' => $slot['sub_activity_name'],
                ]);
            }
        }
        $this->command->info('สร้างข้อมูล SubActivity สำเร็จ');

    }
}
