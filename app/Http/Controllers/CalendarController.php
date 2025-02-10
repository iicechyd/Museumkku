<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookings;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function getEvents(Request $request)
    {
        $bookingsQuery = Bookings::with(['activity', 'timeslot', 'institute'])
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
            ->where('status', 1);

        $bookings = $bookingsQuery->get();

        $filteredBookings = Bookings::with('activity', 'timeslot', 'institute')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1)
                      ->whereIn('activity_id', [1, 2, 3]);
            })
            ->where('status', 1)
            ->get();

        $dailyTotalVisitors = $filteredBookings->groupBy('booking_date')->map(function ($bookingsByDate) {
            return $bookingsByDate->sum(function ($booking) {
                return $this->calculateTotalApproved($booking);
            });
        });

         $totalVisitorEvents = $dailyTotalVisitors->map(function ($total, $date) use ($filteredBookings) {
            $bookingDetails = $filteredBookings->where('booking_date', $date)->map(function ($booking) { 
                return [
                    'activity_name' => $booking->activity->activity_name,
                    'timeslot_id' => $booking->timeslot->timeslots_id ?? '',
                    'start_time' => $booking->timeslot->start_time ?? '',
                    'end_time' => $booking->timeslot->end_time ?? '',
                    'total_approved' => $this->calculateTotalApproved($booking),
                ];
            });

            return [
                'title' => "จำนวนผู้เข้าชม $total คน",
                'start' => $date,
                'allDay' => true,
                'extendedProps' => [
                    'total_visitors' => $total,
                    'booking_details' => $bookingDetails
                ]
            ];
        });

        $events = $this->getGroupedEvents($bookings);
        $allEvents = collect($events)->merge(collect($totalVisitorEvents))->values();
        return response()->json($allEvents);
    }

    private function getGroupedEvents($bookings)
    {
        $groupedBookings = $bookings->groupBy(function ($booking) {
            return $booking->booking_date . '-' . $booking->activity_id . '-' . ($booking->timeslot->timeslots_id ?? 'no_timeslot');
        });

        return $groupedBookings->map(function ($groupedBooking) {
            $firstBooking = $groupedBooking->first();
            $totalApproved = $this->calculateTotalApprovedForGroup($groupedBooking);
            return $this->createEvent($firstBooking, $totalApproved);
        })->values();
    }

    private function calculateTotalApprovedForGroup($groupedBookings)
    {
        return $groupedBookings->sum(function ($booking) {
            return $this->calculateTotalApproved($booking);
        });
    }

    private function calculateTotalApproved($booking)
    {
        return $booking->children_qty + $booking->students_qty + $booking->adults_qty + $booking->kid_qty + $booking->disabled_qty + $booking->elderly_qty + $booking->monk_qty;
    }

    private function createEvent($booking, $totalApproved)
    {
        $startTime = $booking->timeslot ? Carbon::createFromFormat('H:i:s', $booking->timeslot->start_time)->format('H:i') : null;
        $endTime = $booking->timeslot ? Carbon::createFromFormat('H:i:s', $booking->timeslot->end_time)->format('H:i') : null;

        $startDate = Carbon::createFromFormat('Y-m-d', $booking->booking_date);
        $durationDays = $booking->activity->duration_days;
        $endDate = date('Y-m-d', strtotime("+$durationDays days", strtotime($startDate)));

        $endDate = $booking->end_date ?? $booking->booking_date;
        if ($endDate !== $booking->booking_date) {
            $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->addDay()->format('Y-m-d');
        }

        return [
            'title' => $booking->activity->activity_name,
            'start' => $startDate->format('Y-m-d') . ($startTime ? " $startTime" : ''),
            'end'   => $endDate . ($endTime ? " $endTime" : ''),
            'color' => $this->getStatusColor($booking->status),
            'extendedProps' => [
                'activity_name' => $booking->activity->activity_name,
                'start_time'  => $startTime,
                'end_time'    => $endTime,
                'duration_days' => $durationDays,
                'status'      => $booking->status,
                'total_qty'     => $totalApproved,
            ]
        ];
    }

    private function getStatusColor($status)
    {
        return match ($status) {
            1 => '#E6A732',
        };
    }

}
