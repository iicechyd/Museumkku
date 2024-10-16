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

        foreach ($requestBookings as $item) {
            // Calculate total approved bookings for the same booking_date and timeslot
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('timeslots_id', $item->timeslot->timeslots_id)
                ->where('status', 1) // Only count approved bookings
                ->sum(DB::raw('children_qty + students_qty + adults_qty'));

            // For unapproved bookings, keep max_capacity without adjustments
            $item->remaining_capacity = $item->timeslot->max_capacity - $totalApproved;

            // Calculate total price
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;

            // Calculate total price
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;

            // Calculate total visitors
            $item->totalVisitors = $item->children_qty + $item->students_qty + $item->adults_qty;
        }


        return view('admin.activityRequest.request_bookings', compact('requestBookings'));
    }

    function showApprovedActivity()
    {
        $approvedBookings = Bookings::with('activity', 'timeslot')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
            ->where('status', 1)
            ->paginate(5);

        foreach ($approvedBookings as $item) {
            // Calculate total approved bookings for the same booking_date and timeslot
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('timeslots_id', $item->timeslot->timeslots_id)
                ->where('status', 1) // Only count approved bookings
                ->sum(DB::raw('children_qty + students_qty + adults_qty'));

            // For approved bookings, adjust the remaining capacity
            $item->remaining_capacity = $item->timeslot->max_capacity - $totalApproved;

            // Calculate total price
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;

            // Calculate total price
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;

            // Calculate total visitors
            $item->totalVisitors = $item->children_qty + $item->students_qty + $item->adults_qty;
        }

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

        foreach ($exceptBookings as $item) {
            // Calculate total approved bookings for the same booking_date and timeslot
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('timeslots_id', $item->timeslot->timeslots_id)
                ->where('status', 1) // Only count approved bookings
                ->sum(DB::raw('children_qty + students_qty + adults_qty'));

            // Set remaining capacity initially, subtract approved bookings
            $item->remaining_capacity = $item->actvity->max_capacity - $totalApproved;

            // Since the status is 2 (canceled), add back the visitors of this booking to remaining capacity
            $item->remaining_capacity += $item->children_qty + $item->students_qty + $item->adults_qty;

            // Calculate total price (for reference or auditing purposes)
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;

            // Calculate total price
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;

            // Calculate total visitors
            $item->totalVisitors = $item->children_qty + $item->students_qty + $item->adults_qty;
        }

        return view('admin.activityRequest.except_cases_bookings', compact('exceptBookings'));
    }

    // ฟังก์ชันแสดงฟอร์มจองเข้าร่วมกิจกรรม (เฉพาะ activity_type_id = 2)
    public function showActivityBookingForm($activity_id)
    {
        $selectedActivity = Activity::find($activity_id); // Find the selected activity
        if (!$selectedActivity) {
            return redirect()->back()->with('error', 'Activity not found.');
        }
        // Fetch timeslots related to the selected activity
        $timeslots = Timeslots::where('activity_id', $activity_id)->get();

        return view('form_bookings', [
            'activity_id' => $activity_id,
            'selectedActivity' => $selectedActivity,
            'timeslots' => $timeslots,
        ]);

    }

    public function deleteTimeslots($id)
    {
        $timeslot = Timeslots::findOrFail($id);
        $timeslot->delete();

        return redirect()->back()->with('success', 'ลบรอบการเข้าชมสำเร็จ');
    }
}
