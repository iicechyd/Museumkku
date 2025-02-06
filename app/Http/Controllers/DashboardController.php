<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookings;
use App\Models\Activity;
use App\Models\StatusChanges;
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
        $currentYear = Carbon::now()->year;
        $startMonth = now()->month >= 10 ? now()->startOfMonth() : now()->subYear()->startOfMonth()->month(10);
        $endMonth = $startMonth->copy()->addYear()->month(9)->endOfMonth();
        $activities = Activity::all();
        $startMonthThai = $startMonth->locale('th')->isoFormat('MMMM') . ' ' . ($startMonth->year + 543);
        $endMonthThai = $endMonth->locale('th')->isoFormat('MMMM') . ' ' . ($endMonth->year + 543);

        $totalVisitorsToday = StatusChanges::join('bookings', 'status_changes.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2, 3])
            ->whereDate('bookings.booking_date', $today)
            ->where('bookings.status', 2)
            ->sum(DB::raw('actual_children_qty + actual_students_qty + actual_adults_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty'));

        $totalVisitorsThisWeek = StatusChanges::join('bookings', 'status_changes.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2, 3])
            ->whereBetween('bookings.booking_date', [$weekStart, $weekEnd])
            ->where('bookings.status', 2)
            ->sum(DB::raw('actual_children_qty + actual_students_qty + actual_adults_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty'));

        $totalVisitorsThisMonth = StatusChanges::join('bookings', 'status_changes.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2, 3])
            ->whereBetween('bookings.booking_date', [$monthStart, $monthEnd])
            ->where('bookings.status', 2)
            ->sum(DB::raw('actual_children_qty + actual_students_qty + actual_adults_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty'));

        $totalVisitorsThisYear = StatusChanges::join('bookings', 'status_changes.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2, 3])
            ->whereBetween('bookings.booking_date', [$startMonth, $endMonth])
            ->where('bookings.status', 2)
            ->sum(DB::raw('actual_children_qty + actual_students_qty + actual_adults_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty'));

        $totalVisitorsPerMonthThisYear = [];
        $currentDate = $startMonth->copy();
        while ($currentDate <= $endMonth) {
            $monthStart = $currentDate->copy()->startOfMonth()->format('Y-m-d');
            $monthEnd = $currentDate->copy()->endOfMonth()->format('Y-m-d');
            $monthLabel = $currentDate->copy()->translatedFormat('M') . ' ' . ($currentDate->year + 543);

            $totalVisitorsPerMonthThisYear[$monthLabel] = StatusChanges::join('bookings', 'status_changes.booking_id', '=', 'bookings.booking_id')
                ->whereBetween('bookings.booking_date', [$monthStart, $monthEnd])
                ->where('bookings.status', 2)
                ->sum(DB::raw('actual_children_qty + actual_students_qty + actual_adults_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty'));

            $currentDate->addMonth();
        }

        $yearlyRevenueGeneral = [];
        $currentDate = $startMonth->copy();
        while ($currentDate <= $endMonth) {
            $monthStart = $currentDate->copy()->startOfMonth()->format('Y-m-d');
            $monthEnd = $currentDate->copy()->endOfMonth()->format('Y-m-d');
            $monthLabel = $currentDate->copy()->translatedFormat('M Y');

            $yearlyRevenueGeneral[$monthLabel] = Bookings::whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
                ->join('activities', 'bookings.activity_id', '=', 'activities.activity_id')
                ->join('status_changes', 'bookings.booking_id', '=', 'status_changes.booking_id')
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->whereIn('bookings.status', [2])
                ->sum(DB::raw(
                    '(actual_children_qty * children_price) + (actual_students_qty * student_price) + 
                 (actual_adults_qty * adult_price) + (actual_disabled_qty * disabled_price) + 
                 (actual_elderly_qty * elderly_price) + (actual_monk_qty * monk_price)'
                ));
            $currentDate->addMonth();
        }
        $yearlyRevenueActivity = [];
        $currentDate = $startMonth->copy();
        while ($currentDate <= $endMonth) {
            $monthStart = $currentDate->copy()->startOfMonth()->format('Y-m-d');
            $monthEnd = $currentDate->copy()->endOfMonth()->format('Y-m-d');
            $monthLabel = $currentDate->copy()->translatedFormat('M Y');

            $yearlyRevenueActivity[$monthLabel] = Bookings::whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
                ->join('activities', 'bookings.activity_id', '=', 'activities.activity_id')
                ->join('status_changes', 'bookings.booking_id', '=', 'status_changes.booking_id')
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->whereIn('bookings.status', [2])
                ->sum(DB::raw(
                    '(actual_children_qty * children_price) + (actual_students_qty * student_price) + 
                     (actual_adults_qty * adult_price) + (actual_disabled_qty * disabled_price) + 
                     (actual_elderly_qty * elderly_price) + (actual_monk_qty * monk_price)'
                ));
            $currentDate->addMonth();
        }

        $totalVisitorsBooked = [];
        foreach ($activities as $activity) {
            $totalVisitorsBooked[$activity->activity_id] = Bookings::whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
                ->where('activity_id', $activity->activity_id)
                ->whereIn('status', [0, 1, 2, 3])
                ->whereBetween('booking_date', [$startMonth, $endMonth])
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));
        }

        $totalSpecialActivity = DB::table('activities')
            ->leftJoin('activity_types', 'activities.activity_type_id', '=', 'activity_types.activity_type_id')
            ->leftJoin('bookings', function ($join) use ($startMonth, $endMonth) {
                $join->on('activities.activity_id', '=', 'bookings.activity_id')
                    ->whereBetween('bookings.booking_date', [$startMonth, $endMonth]);
            })
            ->leftJoin('status_changes', 'bookings.booking_id', '=', 'status_changes.booking_id')
            ->select(
                'activities.activity_id',
                'activities.activity_name',
                'activities.target_yearly_count',
                DB::raw('
            COALESCE(SUM(status_changes.actual_children_qty + status_changes.actual_students_qty + 
            status_changes.actual_adults_qty + status_changes.actual_disabled_qty + 
            status_changes.actual_elderly_qty + status_changes.actual_monk_qty), 0) as total_visitors
        '),
                DB::raw('COALESCE(COUNT(DISTINCT status_changes.booking_id), 0) as total_bookings')
            )
            ->where('activities.activity_type_id', 2)
            ->groupBy('activities.activity_id', 'activities.activity_name', 'activities.target_yearly_count',)
            ->get();

        $visitorStats = Bookings::join('status_changes', 'bookings.booking_id', '=', 'status_changes.booking_id')
            ->selectRaw('
                SUM(actual_children_qty) as children_qty, 
                SUM(actual_students_qty) as students_qty, 
                SUM(actual_adults_qty) as adults_qty, 
                SUM(actual_disabled_qty) as disabled_qty, 
                SUM(actual_elderly_qty) as elderly_qty, 
                SUM(actual_monk_qty) as monk_qty
            ')
            ->whereBetween('booking_date', [$startMonth, $endMonth])
            ->whereIn('status_changes.new_status', [2])
            ->first();

        return view('admin.dashboard', compact(
            'activities',
            'totalVisitorsToday',
            'totalVisitorsThisWeek',
            'totalVisitorsThisMonth',
            'totalVisitorsThisYear',
            'totalVisitorsPerMonthThisYear',
            'yearlyRevenueGeneral',
            'yearlyRevenueActivity',
            'totalVisitorsBooked',
            'totalSpecialActivity',
            'visitorStats',
            'startMonth',
            'endMonth',
            'startMonthThai',
            'endMonthThai',
        ));
    }
}
