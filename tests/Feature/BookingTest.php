<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     * @dataProvider bookingDataProvider
     */
    public function testInsertBookingValidation($inputData, $expectedStatus)
    {
        $response = $this->post('/InsertBooking', $inputData);
        $response->assertStatus($expectedStatus);
    }

    public static function bookingDataProvider()
    {
        return [
            'valid booking with tmss' => [
                [
                    'fk_activity_id' => 1,
                    'fk_tmss_id' => 1,
                    'booking_date' => '15/01/2025',
                    'instituteName' => 'โรงเรียนบ้านหมีน้อย',
                    'instituteAddress' => '22/01 บ้านหมีน้อย',
                    'province' => 'ขอนแก่น',
                    'district' => 'เมืองขอนแก่น',
                    'subdistrict' => 'ศิลา',
                    'zipcode' => '40000',
                    'visitorName' => 'นางสาวแก้วกานต์ เด่นดี',
                    'visitorEmail' => 'iicechyd.gaming@gmail.com',
                    'tel' => '0987654321',
                    'children_qty' => 100,
                ],
                302, // Expected HTTP status code
            ],
            'valid  Activity booking' => [
                [
                    'fk_activity_id' => 4,
                    'sub_activity_id' => 1,2,
                    'booking_date' => '20/01/2025',
                    'instituteName' => 'Institute B',
                    'instituteAddress' => '456 Secondary St',
                    'province' => 'Bangkok',
                    'district' => 'District B',
                    'subdistrict' => 'Subdistrict B',
                    'zipcode' => '54321',
                    'visitorName' => 'Jane Doe',
                    'visitorEmail' => 'iicechyd.gaming@gmail.com',
                    'tel' => '0987654321',
                    'adults_qty' => 200,
                ],
                302,
            ],
        ];
    }
    /**
     * @test
     * @dataProvider conflictBookingDataProvider
     */
    public function testConflictBooking($inputData, $expectedStatus)
    {
        $this->post('/InsertBooking', [
            'fk_activity_id' => 1,
            'fk_tmss_id' => 1,
            'booking_date' => '19/01/2025',
            'instituteName' => 'โรงเรียนบ้านหมีน้อย',
            'instituteAddress' => '34/02 บ้านทับหมีใหญ่',
            'province' => 'ขอนแก่น',
            'district' => 'เมืองขอนแก่น',
            'subdistrict' => 'ศิลา',
            'zipcode' => '40001',
            'visitorName' => 'นายสมชาย มานะดี',
            'visitorEmail' => 'somchai22@example.com',
            'tel' => '0987654322',
            'children_qty' => 90,
        ]);
        $response = $this->post('/InsertBooking', $inputData);
        $response->assertStatus($expectedStatus);
    }

    public static function conflictBookingDataProvider()
    {
        return [
            'conflicting booking' => [
                [
                    'fk_activity_id' => 3,
                    'fk_tmss_id' => 1,
                    'booking_date' => '19/01/2025',
                    'instituteName' => 'โรงเรียนทับหมีใหญ่',
                    'instituteAddress' => '34/02 บ้านทับหมีใหญ่',
                    'province' => 'ขอนแก่น',
                    'district' => 'เมืองขอนแก่น',
                    'subdistrict' => 'ศิลา',
                    'zipcode' => '40001',
                    'visitorName' => 'นายสมชาย มานะดี',
                    'visitorEmail' => 'somchai@example.com',
                    'tel' => '0987654322',
                    'children_qty' => 50,
                ],
                302, 
            ],
        ];
    }
}
