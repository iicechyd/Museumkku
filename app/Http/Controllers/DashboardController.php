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
        $yearStart = Carbon::today()->startOfYear();
        $yearEnd = Carbon::today()->endOfYear();
        $currentYear = Carbon::now()->year;
        $startMonth = now()->month >= 10 ? now()->startOfMonth() : now()->subYear()->startOfMonth()->month(10);
        $endMonth = $startMonth->copy()->addYear()->month(9)->endOfMonth();
        $activities = Activity::all();

        $totalVisitorsToday = StatusChanges::join('bookings', 'status_changes.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2, 3])
            ->whereDate('bookings.booking_date', $today)
            ->where('bookings.status', 2)
            ->sum('status_changes.number_of_visitors');

        $totalVisitorsThisWeek = StatusChanges::join('bookings', 'status_changes.booking_id', '=', 'bookings.booking_id')
        ->whereIn('bookings.activity_id', [1, 2, 3])
        ->whereBetween('bookings.booking_date', [$weekStart, $weekEnd])
        ->where('bookings.status', 2)
        ->sum('status_changes.number_of_visitors');
        
        $totalVisitorsThisMonth = StatusChanges::join('bookings', 'status_changes.booking_id', '=', 'bookings.booking_id')
        ->whereIn('bookings.activity_id', [1, 2, 3])
        ->whereBetween('bookings.booking_date', [$monthStart, $monthEnd])
        ->where('bookings.status', 2)
        ->sum('status_changes.number_of_visitors');

        $totalVisitorsThisYear = StatusChanges::join('bookings', 'status_changes.booking_id', '=', 'bookings.booking_id')
        ->whereIn('bookings.activity_id', [1, 2, 3])
        ->whereBetween('bookings.booking_date', [$yearStart, $yearEnd])
        ->where('bookings.status', 2)
        ->sum('status_changes.number_of_visitors');

        $totalVisitorsPerMonthThisYear = [];
        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::createFromDate($currentYear, $month, 1)->startOfMonth();
            $endOfMonth = Carbon::createFromDate($currentYear, $month, 1)->endOfMonth();

            $totalVisitorsPerMonthThisYear[$month] = StatusChanges::join('bookings', 'status_changes.booking_id', '=', 'bookings.booking_id')
                ->whereBetween('bookings.booking_date', [$startOfMonth, $endOfMonth])
                ->where('bookings.status', 2)
                ->sum('status_changes.number_of_visitors');
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
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->where('bookings.status', 2)
                ->sum(DB::raw(
                    '(children_qty * children_price) + 
                    (students_qty * student_price) + 
                    (adults_qty * adult_price) + 
                    (disabled_qty * disabled_price) + 
                    (elderly_qty * elderly_price) + 
                    (monk_qty * monk_price)'
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
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->whereIn('bookings.status', [1, 2])
                ->sum(DB::raw(
                    '(children_qty * children_price) + (students_qty * student_price) + 
                            (adults_qty * adult_price) + (disabled_qty * disabled_price) + 
                            (elderly_qty * elderly_price) + (monk_qty * monk_price)'));
            $currentDate->addMonth();
            }

            $totalSpecialActivity = DB::table('activities')
            ->leftJoin('activity_types', 'activities.activity_type_id', '=', 'activity_types.activity_type_id')
            ->leftJoin('bookings', function($join) use ($yearStart, $yearEnd) {
                $join->on('activities.activity_id', '=', 'bookings.activity_id')
                     ->whereBetween('bookings.booking_date', [$yearStart, $yearEnd]);
            })
            ->select(
       'activities.activity_id',
                'activities.activity_name',
                'activities.target_yearly_count',
                DB::raw('
                    COALESCE(SUM(bookings.children_qty + bookings.students_qty + bookings.adults_qty +
                    bookings.disabled_qty + bookings.elderly_qty + bookings.monk_qty), 0) as total_visitors
                '),
                DB::raw('COALESCE(COUNT(bookings.booking_id), 0) as total_bookings')
            )
            ->where('activities.activity_type_id', 2)
            ->groupBy('activities.activity_id', 'activities.activity_name','activities.target_yearly_count',)
            ->get();

        $totalVisitorsBooked = [];
        foreach ($activities as $activity) {
            $totalVisitorsBooked[$activity->activity_id] = Bookings::whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
                ->where('activity_id', $activity->activity_id)
                ->whereIn('status', [0, 1, 2])
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));
        }
        $visitorStats = Bookings::selectRaw('
            SUM(children_qty) as children_qty, SUM(students_qty) as students_qty, 
            SUM(adults_qty) as adults_qty, SUM(disabled_qty) as disabled_qty, 
            SUM(elderly_qty) as elderly_qty, SUM(monk_qty) as monk_qty')
            ->whereBetween('booking_date', [$yearStart, $yearEnd])
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
        ));
    }
}
