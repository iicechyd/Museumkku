<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bookings;
use App\Models\Timeslots;
use App\Models\Activity;

class BookingActivityController extends Controller
{
    function showBookingsActivity(Request $request)
    {
        $activities = Activity::where('activity_type_id', 2)->get();

        $query = Bookings::with('activity', 'timeslot', 'visitor', 'institute')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
            ->where('status', 0);

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        $requestBookings = $query->paginate(5);

        foreach ($requestBookings as $item) {
            $totalApproved = 0;

            if ($item->timeslot) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('timeslots_id', $item->timeslot->timeslots_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));
            } else {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty +  monk_qty'));
            }
            $maxCapacity = $item->activity->max_capacity;
            
            if ($maxCapacity === null) {
                $item->remaining_capacity = 'ไม่จำกัดจำนวนคน';
            } else {
                $item->remaining_capacity = $maxCapacity - $totalApproved;
            }

            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;
        }
        return view('admin.activityRequest.request_bookings', compact('requestBookings', 'activities'));
    }

    function showApprovedActivity(Request $request)
    {
        $activities = Activity::where('activity_type_id', 2)->get();

        $query = Bookings::with('activity', 'timeslot', 'visitor', 'institute')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
            ->where('status', 1);

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        $approvedBookings = $query->paginate(5);

        foreach ($approvedBookings as $item) {
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('activity_id', $item->activity_id) 
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));

            if ($item->timeslot && $item->timeslot->timeslots_id) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('timeslots_id', $item->timeslot->timeslots_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty +  monk_qty'));
            }

            if ($item->activity->max_capacity !== null) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
            } else {
                $item->remaining_capacity = 'ไม่จำกัดจำนวนคน';
            }
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;
        }
        return view('admin.activityRequest.approved_bookings', compact('approvedBookings', 'activities'));
    }

    function showExceptActivity(Request $request)
    {
        $activities = Activity::where('activity_type_id', 2)->get();

        $query = Bookings::with('activity', 'timeslot', 'visitor', 'institute')
        ->whereHas('activity', function ($query) {
            $query->where('activity_type_id', 2);
        })
        ->where('status', 2);
        
        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        $exceptBookings = $query->paginate(5);

        foreach ($exceptBookings as $item) {
            $totalApproved = 0;
            if ($item->timeslot) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('timeslots_id', $item->timeslot->timeslots_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty +  monk_qty'));
            }
            if ($item->activity) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
                $item->remaining_capacity += $item->children_qty + $item->students_qty + $item->adults_qty + $item->disabled_qty + $item->elderly_qty +  $item->monk_qty;
            } else {
                $item->remaining_capacity = 'N/A';
            }
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;
        }
        return view('admin.activityRequest.except_cases_bookings', compact('exceptBookings', 'activities'));
    }

    public function deleteTimeslots($id)
    {
        $timeslot = Timeslots::findOrFail($id);
        $timeslot->delete();

        return redirect()->back()->with('success', 'ลบรอบการเข้าชมสำเร็จ');
    }
}
