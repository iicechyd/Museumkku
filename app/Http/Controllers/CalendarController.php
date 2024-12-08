<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookings;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function getEvents(Request $request)
{
    $bookings = Bookings::with('activity', 'timeslot')->get();

    $events = $bookings->map(function ($booking) {
        $startTime = $booking->timeslot ? Carbon::createFromFormat('H:i:s', $booking->timeslot->start_time)->format('H:i') : null;
        $endTime = $booking->timeslot ? Carbon::createFromFormat('H:i:s', $booking->timeslot->end_time)->format('H:i') : null;

        // ตรวจสอบวันที่สิ้นสุด และเพิ่ม 1 วันหากเป็นช่วงเวลาหลายวัน
        $endDate = $booking->end_date ?? $booking->booking_date;
        if ($endDate !== $booking->booking_date) {
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->addDay()->format('Y-m-d');
        }

        return [
            'title' => $booking->activity->activity_name . " (สถานะการจอง: " . $this->getStatusText($booking->status) . ")",
            'start' => $booking->booking_date . ($startTime ? " $startTime" : ''),
            'end'   => $endDate . ($endTime ? " $endTime" : ''),
            'color' => $this->getStatusColor($booking->status),
            'extendedProps' => [
                'description' => $booking->activity->description,
                'start_time'  => $startTime,
                'end_time'    => $endTime,
                'status'      => $booking->status,
            ]
        ];
    });

    return response()->json($events);
}

private function getStatusColor($status)
{
    return match ($status) {
        0 => '#ffc107',   // รออนุมัติ
        1 => '#28a745',   // อนุมัติ
        2 => '#dc3545',   // ยกเลิก
        default => '#000000',
    };
}

private function getStatusText($status)
{
    return match ($status) {
        0 => 'รออนุมัติ',
        1 => 'อนุมัติ',
        2 => 'ยกเลิก',
        default => 'ไม่ทราบสถานะ',
    };
}

}
