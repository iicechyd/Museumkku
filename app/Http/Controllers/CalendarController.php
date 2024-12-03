<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookings;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function getEvents(Request $request)
    {
        $bookings = Bookings::with('activity', 'timeslot')
        ->get();

    $events = $bookings->map(function ($booking) {
        $startTime = $booking->timeslot ? Carbon::createFromFormat('H:i:s', $booking->timeslot->start_time)->format('H:i') : '00:00';
        $endTime = $booking->timeslot ? Carbon::createFromFormat('H:i:s', $booking->timeslot->end_time)->format('H:i') : '23:59';
        
        return [
            'title' => $booking->activity->activity_name . " (สถานะการจอง: " . $this->getStatusText($booking->status) . ")",
            'start' => $booking->booking_date . ' ' . $startTime,
            'end' => $booking->booking_date . ' ' . $endTime,
            'color' => $this->getStatusColor($booking->status),
            'extendedProps' => [
                    'description' => $booking->activity->description,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => $booking->status,
                ]
        ];
    });
        return response()->json($events);
    }

    private function getStatusColor($status)
    {
        switch ($status) {
            case 0:
                return '#ffc107';
            case 1:
                return '#28a745';
            case 2:
                return '#dc3545';
        }
    }

    private function getStatusText($status)
    {
        switch ($status) {
            case 0:
                return 'รออนุมัติ';
            case 1:
                return 'อนุมัติ';
            case 2:
                return 'ยกเลิก';
        }
    }
}
