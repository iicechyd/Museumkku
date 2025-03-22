<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Models\Bookings;
use App\Models\Tmss;
use App\Models\Activity;
use Carbon\Carbon;

class BookingActivityController extends Controller
{
    function showTodayBookings(Request $request)
    {
        $today = Carbon::today();

        $activities = Activity::where('activity_type_id', 2)
        ->get()
        ->map(function ($activity) use ($today) {
            $countBookings = Bookings::where('activity_id', $activity->activity_id)
                ->whereDate('booking_date', $today)
                ->where('status', 1)
                ->count();

            $activity->countBookings = $countBookings;
            return $activity;
        });

        $query = Bookings::with('activity', 'tmss', 'visitor', 'institute', 'documents', 'subactivities', 'user')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
            ->where('status', 1)
            ->whereDate('booking_date', $today);

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        $approvedBookings = $query->paginate(5);


        foreach ($approvedBookings as $item) {
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('activity_id', $item->activity_id)
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));

            if ($item->tmss && $item->tmss->tmss_id) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('tmss_id', $item->tmss->tmss_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));
            }

            if ($item->activity->max_capacity !== null) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
            } else {
                $item->remaining_capacity = 'ไม่จำกัดจำนวนคน';
            }

            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $kidPrice = $item->kid_qty * $item->activity->kid_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice + $kidPrice + $disabledPrice + $elderlyPrice + $monkPrice;
        }

        return view('admin.activityRequest.manage_bookings', compact('approvedBookings', 'activities'));
    }
    function showBookingsActivity(Request $request)
    {
        $activities = Activity::where('activity_type_id', 2)
        ->get()
        ->map(function ($activity) {
            $countBookings = Bookings::where('activity_id', $activity->activity_id)
                ->where('status', 0)
                ->count();

            $activity->countBookings = $countBookings;
            return $activity;
        });

        $query = Bookings::with('activity', 'tmss', 'visitor', 'institute', 'subactivities', 'user')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
            ->where('status', 0);

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        $requestBookings = $query->paginate(5);

        foreach ($requestBookings as $item) {
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('activity_id', $item->activity_id)
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));

            if ($item->tmss && $item->tmss->tmss_id) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('tmss_id', $item->tmss->tmss_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));
            }

            if ($item->activity->max_capacity !== null) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
            } else {
                $item->remaining_capacity = 'ไม่จำกัดจำนวนคน';
            }

            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $kidPrice = $item->kid_qty * $item->activity->kid_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice + $kidPrice + $disabledPrice + $elderlyPrice + $monkPrice;
        }
        return view('admin.activityRequest.request_bookings', compact('requestBookings', 'activities'));
    }

    function showApprovedActivity(Request $request)
    {
        $activities = Activity::where('activity_type_id', 2)
        ->get()
        ->map(function ($activity) {
            $countBookings = Bookings::where('activity_id', $activity->activity_id)
                ->where('status', 1)
                ->count();

            $activity->countBookings = $countBookings;
            return $activity;
        });

        $query = Bookings::with('activity', 'tmss', 'visitor', 'institute', 'subactivities', 'user')
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
                ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty + monk_qty'));

            if ($item->tmss && $item->tmss->tmss_id) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('tmss_id', $item->tmss->tmss_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));
            }

            if ($item->activity->max_capacity !== null) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
            } else {
                $item->remaining_capacity = 'ไม่จำกัดจำนวนคน';
            }
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $kidPrice = $item->kid_qty * $item->activity->kid_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice + $kidPrice + $disabledPrice + $elderlyPrice + $monkPrice;
            $item->signed_edit_url = URL::signedRoute('admin.edit_booking', ['booking_id' => $item->booking_id]);

        }
        return view('admin.activityRequest.approved_bookings', compact('approvedBookings', 'activities'));
    }

    function showExceptActivity(Request $request)
    {
        $activities = Activity::where('activity_type_id', 2)
        ->get()
        ->map(function ($activity) {
            $countBookings = Bookings::where('activity_id', $activity->activity_id)
                ->where('status', 3)
                ->count();

            $activity->countBookings = $countBookings;
            return $activity;
        });

        $query = Bookings::with('activity', 'tmss', 'visitor', 'institute', 'subactivities', 'user')
        ->whereHas('activity', function ($query) {
            $query->where('activity_type_id', 2);
        })
        ->where('status', 3);
        
        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        $exceptBookings = $query->paginate(5);

        foreach ($exceptBookings as $item) {
            $totalApproved = 0;
            if ($item->tmss) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('tmss_id', $item->tmss->tmss_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));
            }
            if ($item->activity) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
                $item->remaining_capacity += $item->children_qty + $item->students_qty + $item->adults_qty + $item->kid_qty + $item->disabled_qty + $item->elderly_qty +  $item->monk_qty;
            } else {
                $item->remaining_capacity = 'N/A';
            }
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $kidPrice = $item->kid_qty * $item->activity->kid_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice + $kidPrice + $disabledPrice + $elderlyPrice + $monkPrice;
        }
        return view('admin.activityRequest.except_cases_bookings', compact('exceptBookings', 'activities'));
    }

    public function deleteTmss($id)
    {
        $tmss = Tmss::findOrFail($id);
        $tmss->delete();

        return redirect()->back()->with('success', 'ลบรอบการเข้าชมสำเร็จ');
    }
}
