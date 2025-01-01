<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookings;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $today = Carbon::today();
        $weekStart = Carbon::today()->startOfWeek();
        $weekEnd = Carbon::today()->endOfWeek();
        $monthStart = Carbon::today()->startOfMonth();
        $monthEnd = Carbon::today()->endOfMonth();
        $yearStart = Carbon::today()->startOfYear();
        $yearEnd = Carbon::today()->endOfYear();
        $currentYear = Carbon::now()->year;

        $activities = Activity::all();

        $totalVisitorsToday = [];
        foreach ($activities as $activity) {
            $totalVisitorsToday[$activity->activity_id] = Bookings::where('activity_id', $activity->activity_id)
                ->whereDate('booking_date', $today)
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));
        }

        // จำนวนผู้เข้าชมทั้งหมดสำหรับแต่ละกิจกรรมในสัปดาห์นี้
        $totalVisitorsThisWeek = [];
        foreach ($activities as $activity) {
            $totalVisitorsThisWeek[$activity->activity_id] = Bookings::where('activity_id', $activity->activity_id)
                ->whereBetween('booking_date', [$weekStart, $weekEnd])
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));
        }

        // จำนวนผู้เข้าชมทั้งหมดสำหรับแต่ละกิจกรรมในเดือนนี้
        $totalVisitorsThisMonth = [];
        foreach ($activities as $activity) {
            $totalVisitorsThisMonth[$activity->activity_id] = Bookings::where('activity_id', $activity->activity_id)
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));
        }

        $totalVisitorsThisYear = [];
        foreach ($activities as $activity) {
            $totalVisitorsThisYear[$activity->activity_id] = Bookings::where('activity_id', $activity->activity_id)
                ->whereBetween('booking_date', [$yearStart, $yearEnd])
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));
        }

        $totalVisitorsPerDayType1 = [];
        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        foreach ($daysOfWeek as $index => $day) {
            $startOfDay = $weekStart->copy()->addDays($index)->format('Y-m-d');
            $endOfDay = $weekStart->copy()->addDays($index)->format('Y-m-d');
            $totalVisitorsPerDayType1[$day] = Bookings::whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
                ->whereBetween('booking_date', [$startOfDay, $endOfDay])
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty')); // คำนวณจำนวนผู้เข้าชมทั้งหมด
        }

        $totalVisitorsPerMonthThisYear = [];
        // คำนวณจำนวนผู้เข้าชมแต่ละเดือน
        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth();
            $endOfMonth = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth();

            $totalVisitorsPerMonthThisYear[$month] = Bookings::whereBetween('booking_date', [$startOfMonth, $endOfMonth])
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));
        }

    $totalVisitorsThisYear = [];
    foreach ($activities as $activity) {
        // Get the total number of bookings for this activity in the current year
        $totalVisitorsThisYear[$activity->activity_id] = Bookings::where('activity_id', $activity->activity_id)
            ->whereBetween('booking_date', [$yearStart, $yearEnd])
            ->where('status', 1)
            ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));
    }

        $specialActivities = DB::table('activities')
            ->leftJoin('activity_types', 'activities.activity_type_id', '=', 'activity_types.activity_type_id')
            ->leftJoin('bookings', 'activities.activity_id', '=', 'bookings.activity_id')
            ->select(
                'activities.activity_id',
                'activities.activity_name',
                DB::raw('
            COALESCE(SUM(bookings.children_qty + bookings.students_qty + bookings.adults_qty +
            bookings.disabled_qty + bookings.elderly_qty + bookings.monk_qty), 0) as total_visitors
        '),
                DB::raw('COALESCE(COUNT(bookings.booking_id), 0) as total_bookings')
            )
            ->where('activities.activity_type_id', 2)
            ->groupBy('activities.activity_id', 'activities.activity_name')
            ->get();

        $totalVisitors = [];
        foreach ($activities as $activity) {
            $totalVisitors[$activity->activity_id] = Bookings::whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
                ->where('activity_id', $activity->activity_id)
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));
        }
        $visitorStats = Bookings::selectRaw('
        SUM(children_qty) as children_qty, 
        SUM(students_qty) as students_qty, 
        SUM(adults_qty) as adults_qty, 
        SUM(disabled_qty) as disabled_qty, 
        SUM(elderly_qty) as elderly_qty, 
        SUM(monk_qty) as monk_qty
    ')
            ->first();

        return view('admin.dashboard', compact(
            'activities',
            'totalVisitorsToday',
            'totalVisitorsThisWeek',
            'totalVisitorsThisMonth',
            'totalVisitorsThisYear',
            'totalVisitors',
            'specialActivities',
            'visitorStats',
            'totalVisitorsPerDayType1',
            'totalVisitorsPerMonthThisYear',
        ));
    }
}
