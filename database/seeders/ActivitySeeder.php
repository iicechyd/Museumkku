<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activity = [
            [
                'activity_id' => 1,
                'activity_type_id' => 1,
                'activity_name' => 'พิพิธภัณฑ์ธรรมชาติวิทยา',
                'description' => 'จัดแสดงนิทรรศการประวัติศาสตร์ทางธรรมชาติวิทยานับตั้งแต่การกำเนิดโลกสิ่งมีชีวิต ความหลากหลายทางชีวภาพ โดยผู้เข้าชมสามารถมีส่วนร่วมในการเรียนรู้และค้นหาคำตอบได้ด้วยตนเอง ผ่านชิ้นงานทางวิทยาศาสตร์และสื่อประสมที่สามารถโต้ตอบกับผู้เข้าชมได้ โดยเนื้อหาการจัดแสดงเน้นการนำเสนอข้อมูลทรัพยากรสำคัญที่พบในภาคตะวันออกเฉียงเหนือ',
                'children_price' => 10,
                'student_price' => 30,
                'adult_price' => 50,
                'max_capacity' => 250,
                'status' => 'active',
                'image' => 'activity_20241128_150258.jpg',
            ],
            [
                'activity_id' => 2,
                'activity_type_id' => 1,
                'activity_name' => 'พิพิธภัณฑ์วิทยาศาสตร์',
                'description' => 'จัดแสดงนิทรรศการด้านวิทยาศาสตร์และเทคโนโลยี พื้นที่กว่า 12,000 ตร.ม. ที่มีทั้งเรื่องราวของภูมิปัญญาวิถีไทยพัฒนาเป็นองค์ความรู้ที่เข้ากับชีวิตประจำวัน, พลังงานธรรมชาติ, การเทคโนโลยีในชีวิตประจำวัน เเละจำลองรูปแบบการใช้เทคโนโลยีในอนาคตเป็นอีกหนึ่งสถานที่ที่เหมาะกับทุกวัย',
                'children_price' => 10,
                'student_price' => 30,
                'adult_price' => 50,
                'max_capacity' => 250,
                'status' => 'active',
                'image' => 'activity_20241128_150304.jpg',

            ],
            [
                'activity_id' => 3,
                'activity_type_id' => 1,
                'activity_name' => 'เข้าชมทั้งสองพิพิธภัณฑ์ (ธรรมชาติวิทยา และวิทยาศาสตร์)',
                'description' => 'จัดแสดงนิทรรศการประวัติศาสตร์ทางธรรมชาติวิทยานับตั้งแต่การกำเนิดโลกสิ่งมีชีวิต ความหลากหลายทางชีวภาพ โดยผู้เข้าชมสามารถมีส่วนร่วมในการเรียนรู้และค้นหาคำตอบได้ด้วยตนเอง ผ่านชิ้นงานทางวิทยาศาสตร์และสื่อประสมที่สามารถโต้ตอบกับผู้เข้าชมได้ โดยเนื้อหาการจัดแสดงเน้นการนำเสนอข้อมูลทรัพยากรสำคัญที่พบในภาคตะวันออกเฉียงเหนือ จัดแสดงนิทรรศการด้านวิทยาศาสตร์และเทคโนโลยี พื้นที่กว่า 12,000 ตร.ม. ที่มีทั้งเรื่องราวของภูมิปัญญาวิถีไทยพัฒนาเป็นองค์ความรู้ที่เข้ากับชีวิตประจำวัน, พลังงานธรรมชาติ, การเทคโนโลยีในชีวิตประจำวัน เเละจำลองรูปแบบการใช้เทคโนโลยีในอนาคตเป็นอีกหนึ่งสถานที่ที่เหมาะกับทุกวัย',
                'children_price' => 15,
                'student_price' => 50,
                'adult_price' => 80,
                'max_capacity' => 500,
                'status' => 'active',
                'image' => 'activity_20241128_150311.jpg',

            ],
            [
                'activity_id' => 4,
                'activity_type_id' => 2,
                'activity_name' => 'ONE DAY CAMP กิจกรรมค่ายวิทยาศาสตร์ 1 วัน',
                'description' => 'กิจกรรมค่ายวิทยาสตร์ รูปแบบการเรียนรู้ทางด้านทฤษฎีและปฏิบัติ ในเรื่องของธรรมชาติ และวิทยาศาสตร์ สนุกกับการเรียนรู้จากหัวข้อต่างๆภายใน 1 วัน',
                'children_price' => 499,
                'student_price' => 499,
                'adult_price' => 499,
                'max_capacity' => null,
                'status' => 'active',
                'image' => 'activity_20241128_150321.jpg',

            ],
            [
                'activity_id' => 5,
                'activity_type_id' => 2,
                'activity_name' => 'TWO DAYS CAMP กิจกรรมค่ายวิทยาศาสตร์ 2 วัน',
                'description' => 'กิจกรรมค่ายวิทยาสตร์ รูปแบบการเรียนรู้ทางด้านทฤษฎีและปฏิบัติ ในเรื่องของธรรมชาติ และวิทยาศาสตร์ สนุกกับการเรียนรู้จากหัวข้อต่างๆภายใน 1 วัน',
                'children_price' => 1999,
                'student_price' => 1999,
                'adult_price' => 1999,
                'max_capacity' => null,
                'status' => 'active',
                'image' => 'activity_20241128_150329.jpg',

            ],
            [
                'activity_id' => 6,
                'activity_type_id' => 2,
                'activity_name' => 'SCIENCE WALK RALLY กิจกรรมฐานวิทยาศาสตร์',
                'description' => 'กิจกรรมฐานการเรียนรู้วิทยาศาสตร์ และธรรมชาติ ที่จะต้องผ่านแต่ละฐาน โดยมีแบบฝึกหัดบันทึกกิจกรรม ผู้เข้าร่วมจะได้ฝึกทักษะ การสังเกต การใช้ประสาทสัมผัส และการทำงานเป็นกลุ่ม',
                'children_price' => 150,
                'student_price' => 150,
                'adult_price' => 150,
                'max_capacity' => null,
                'status' => 'active',
                'image' => 'activity_20241128_150336.jpg',

            ],
            [
                'activity_id' => 7,
                'activity_type_id' => 2,
                'activity_name' => 'SCIENCE SHOW การแสดงทางวิทยาศาสตร์',
                'description' => 'กิจกรรมทางวิทยาศาสตร์ โดยใช้เทคนิคการเล่นกลทางวิทยาศาสตร์ อธิบายหลักการทางวิทยาศาสตร์ให้สนุกเพลิดเพลิน และตื่นตาตื่นใจ',
                'children_price' => 2000,
                'student_price' => 2000,
                'adult_price' => 2000,
                'max_capacity' => null,
                'status' => 'active',
                'image' => 'activity_20241128_150343.jpg',

            ],
        ];
        foreach ($activity as $item) {
            $filePath = 'images/' . $item['image'];
if (!Storage::disk('public')->exists($filePath)) {
    $this->command->warn("ไม่พบไฟล์: {$item['image']}");
    continue;
}


            Activity::create([
                'activity_type_id' => $item['activity_type_id'],
                'activity_name' => $item['activity_name'],
                'description' => $item['description'],
                'children_price' => $item['children_price'],
                'student_price' => $item['student_price'],
                'adult_price' => $item['adult_price'],
                'max_capacity' => $item['max_capacity'],
                'status' => $item['status'],
                'image' => 'images/' . $item['image'],
            ]);
        }

        $this->command->info('สร้างข้อมูลกิจกรรมพร้อมรูปภาพสำเร็จ');
    }
}
