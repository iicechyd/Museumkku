<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bookings;
use App\Models\Timeslots;
use App\Models\Activity;

class BookingActivityController extends Controller
{
    function showBookingsActivity()
    {
        $requestBookings = Bookings::with(['activity', 'timeslot'])
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
            ->where('status', 0)
            ->paginate(5);

        // Add remaining capacity to each booking
        foreach ($requestBookings as $item) {
            // Calculate total booked for the same booking_date and timeslot
            $totalBooked = Bookings::where('booking_date', $item->booking_date)
                ->where('timeslots_id', $item->timeslot->timeslots_id)
                ->where('status', 0)
                ->sum(DB::raw('children_qty + students_qty + adults_qty'));

            $item->remaining_capacity = $item->timeslot->max_capacity - $totalBooked;

            // Check if activity exists before accessing its properties
            if ($item->activity) {
                $childrenPrice = $item->children_qty * $item->activity->children_price;
                $studentPrice = $item->students_qty * $item->activity->student_price;
                $adultPrice = $item->adults_qty * $item->activity->adult_price;

                // Calculate total price
                $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;
            } else {
                // Handle case where activity is null
                $item->totalPrice = 0; // หรือกำหนดค่าเริ่มต้นอื่น ๆ ตามที่ต้องการ
            }

            // Calculate total visitors
            $item->totalVisitors = $item->children_qty + $item->students_qty + $item->adults_qty;
        }

        return view('admin.activityRequest.request_bookings', compact('requestBookings'));
    }

    public function deleteTimeslots($id)
    {
        $timeslot = Timeslots::findOrFail($id);
        $timeslot->delete();

        return redirect()->back()->with('success', 'ลบรอบการเข้าชมสำเร็จ');
    }


    function showApprovedActivity()
    {
        $approvedBookings = Bookings::with('activity', 'timeslot')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
            ->where('status', 1) // สถานะอนุมัติ
            ->paginate(5);
        return view('admin.activityRequest.approved_bookings', compact('approvedBookings'));
    }

    function showExceptActivity()
    {
        $exceptBookings = Bookings::with('activity', 'timeslot')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
            ->where('status', 2)
            ->paginate(5);
        return view('admin.activityRequest.except_cases_bookings', compact('exceptBookings'));
    }

}
