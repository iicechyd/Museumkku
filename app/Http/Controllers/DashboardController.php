<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookings;
use App\Models\Activity;
use App\Models\StatusChanges;
use App\Models\ActualVisitors;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        $weekStart = Carbon::today()->startOfWeek();
        $weekEnd = Carbon::today()->endOfWeek();
        $lastWeekStart = Carbon::parse($weekStart)->subWeek()->format('Y-m-d');
        $lastWeekEnd = Carbon::parse($weekEnd)->subWeek()->format('Y-m-d');
        $monthStart = Carbon::today()->startOfMonth();
        $monthEnd = Carbon::today()->endOfMonth();
        $currentYear = Carbon::now()->year;
        $startMonth = now()->month >= 10 ? now()->startOfMonth() : now()->subYear()->startOfMonth()->month(10);
        $endMonth = $startMonth->copy()->addYear()->month(9)->endOfMonth();
        $activities = Activity::all();
        $startMonthThai = $startMonth->locale('th')->isoFormat('MMMM') . ' ' . ($startMonth->year + 543);
        $endMonthThai = $endMonth->locale('th')->isoFormat('MMMM') . ' ' . ($endMonth->year + 543);

        $visitorsToday = ActualVisitors::join('bookings', 'actual_visitors.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2])
            ->whereDate('bookings.booking_date', $today)
            ->where('bookings.status', 2)
            ->select(
                'bookings.activity_id',
                DB::raw('SUM(actual_children_qty + actual_students_qty + actual_adults_qty + actual_kid_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty + actual_free_teachers_qty) as total')
            )
            ->groupBy('bookings.activity_id')
            ->pluck('total', 'bookings.activity_id');

        $visitorsActivity3Today = ActualVisitors::join('bookings', 'actual_visitors.booking_id', '=', 'bookings.booking_id')
            ->where('bookings.activity_id', 3)
            ->whereDate('bookings.booking_date', $today)
            ->where('bookings.status', 2)
            ->sum(DB::raw('actual_children_qty + actual_students_qty + actual_adults_qty + actual_kid_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty + actual_free_teachers_qty'));

        $totalVisitorsToday = [
            1 => ($visitorsToday[1] ?? 0) + $visitorsActivity3Today,
            2 => ($visitorsToday[2] ?? 0) + $visitorsActivity3Today
        ];

        $visitorsYesterday = ActualVisitors::join('bookings', 'actual_visitors.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2])
            ->whereDate('bookings.booking_date', $yesterday)
            ->where('bookings.status', 2)
            ->select(
                'bookings.activity_id',
                DB::raw('SUM(actual_children_qty + actual_students_qty + actual_adults_qty + actual_kid_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty + actual_free_teachers_qty) as total')
            )
            ->groupBy('bookings.activity_id')
            ->pluck('total', 'bookings.activity_id');

        $visitorsActivity3Yesterday = ActualVisitors::join('bookings', 'actual_visitors.booking_id', '=', 'bookings.booking_id')
            ->where('bookings.activity_id', 3)
            ->whereDate('bookings.booking_date', $yesterday)
            ->where('bookings.status', 2)
            ->sum(DB::raw('actual_children_qty + actual_students_qty + actual_adults_qty + actual_kid_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty + actual_free_teachers_qty'));

        $totalVisitorsYesterday = [
            1 => ($visitorsYesterday[1] ?? 0) + $visitorsActivity3Yesterday,
            2 => ($visitorsYesterday[2] ?? 0) + $visitorsActivity3Yesterday
        ];

        $percentageChangeToday = [];
        foreach ([1, 2] as $activity_id) {
            $previous = $totalVisitorsYesterday[$activity_id] ?? 0;
            $current = $totalVisitorsToday[$activity_id] ?? 0;

            if ($previous > 0) {
                $percentageChangeToday[$activity_id] = (($current - $previous) / $previous) * 100;
            } else {
                $percentageChangeToday[$activity_id] = $current > 0 ? 100 : 0;
            }
        }

        $activityVisitorCountsWeek = ActualVisitors::join('bookings', 'actual_visitors.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2, 3])
            ->whereBetween('bookings.booking_date', [$weekStart, $weekEnd])
            ->where('bookings.status', 2)
            ->select(
                'bookings.activity_id',
                DB::raw('SUM(actual_children_qty + actual_students_qty + actual_adults_qty + actual_kid_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty + actual_free_teachers_qty) as total_visitors')
            )
            ->groupBy('bookings.activity_id')
            ->pluck('total_visitors', 'bookings.activity_id');

        $activity1Visitors = $activityVisitorCountsWeek[1] ?? 0;
        $activity2Visitors = $activityVisitorCountsWeek[2] ?? 0;
        $activity3Visitors = $activityVisitorCountsWeek[3] ?? 0;

        if ($activity3Visitors > 0) {
            $activity1Visitors += $activity3Visitors;
            $activity2Visitors += $activity3Visitors;
        }
        $totalVisitorsThisWeek = [
            1 => $activity1Visitors,
            2 => $activity2Visitors
        ];

        $activityVisitorCountsLastWeek = ActualVisitors::join('bookings', 'actual_visitors.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2, 3])
            ->whereBetween('bookings.booking_date', [$lastWeekStart, $lastWeekEnd])
            ->where('bookings.status', 2)
            ->select(
                'bookings.activity_id',
                DB::raw('SUM(actual_children_qty + actual_students_qty + actual_adults_qty + actual_kid_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty + actual_free_teachers_qty) as total_visitors')
            )
            ->groupBy('bookings.activity_id')
            ->pluck('total_visitors', 'bookings.activity_id');

        $percentageChangeThisWeek = [];
        foreach ([1, 2] as $activity_id) {
            $previous = $activityVisitorCountsLastWeek[$activity_id] ?? 0;
            $current = $totalVisitorsThisWeek[$activity_id] ?? 0;
            if ($previous > 0) {
                $percentageChangeThisWeek[$activity_id] = (($current - $previous) / $previous) * 100;
            } else {
                $percentageChangeThisWeek[$activity_id] = $current > 0 ? 100 : 0;
            }
        }

        $activityVisitorCountsMonth = ActualVisitors::join('bookings', 'actual_visitors.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2, 3])
            ->whereBetween('bookings.booking_date', [$monthStart, $monthEnd])
            ->where('bookings.status', 2)
            ->select(
                'bookings.activity_id',
                DB::raw('SUM(actual_children_qty + actual_students_qty + actual_adults_qty + actual_kid_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty + actual_free_teachers_qty) as total_visitors')
            )
            ->groupBy('bookings.activity_id')
            ->pluck('total_visitors', 'bookings.activity_id');

        $activity1VisitorsMonth = $activityVisitorCountsMonth[1] ?? 0;
        $activity2VisitorsMonth = $activityVisitorCountsMonth[2] ?? 0;
        $activity3VisitorsMonth = $activityVisitorCountsMonth[3] ?? 0;

        if ($activity3VisitorsMonth > 0) {
            $activity1VisitorsMonth += $activity3VisitorsMonth;
            $activity2VisitorsMonth += $activity3VisitorsMonth;
        }

        $totalVisitorsThisMonth = [
            1 => $activity1VisitorsMonth,
            2 => $activity2VisitorsMonth
        ];

        $lastMonthStart = $monthStart->clone()->subMonth()->startOfMonth();
        $lastMonthEnd = $monthStart->clone()->subMonth()->endOfMonth();

        $activityVisitorCountsLastMonth = ActualVisitors::join('bookings', 'actual_visitors.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2, 3])
            ->whereBetween('bookings.booking_date', [$lastMonthStart, $lastMonthEnd])
            ->where('bookings.status', 2)
            ->select(
                'bookings.activity_id',
                DB::raw('SUM(actual_children_qty + actual_students_qty + actual_adults_qty + actual_kid_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty + actual_free_teachers_qty) as total_visitors')
            )
            ->groupBy('bookings.activity_id')
            ->pluck('total_visitors', 'bookings.activity_id');

        $activity1VisitorsLastMonth = $activityVisitorCountsLastMonth[1] ?? 0;
        $activity2VisitorsLastMonth = $activityVisitorCountsLastMonth[2] ?? 0;
        $activity3VisitorsLastMonth = $activityVisitorCountsLastMonth[3] ?? 0;

        if ($activity3VisitorsLastMonth > 0) {
            $activity1VisitorsLastMonth += $activity3VisitorsLastMonth;
            $activity2VisitorsLastMonth += $activity3VisitorsLastMonth;
        }

        $totalVisitorsLastMonth = [
            1 => $activity1VisitorsLastMonth,
            2 => $activity2VisitorsLastMonth
        ];
        $percentageChangeMonth = [];
        foreach ([1, 2] as $activity_id) {
            $previous = $totalVisitorsLastMonth[$activity_id] ?? 0;
            $current = $totalVisitorsThisMonth[$activity_id] ?? 0;

            if ($previous > 0) {
                $percentageChangeMonth[$activity_id] = (($current - $previous) / $previous) * 100;
            } elseif ($previous == 0 && $current > 0) {
                $percentageChangeMonth[$activity_id] = 100;
            } elseif ($previous == 0 && $current == 0) {
                $percentageChangeMonth[$activity_id] = 0;
            } else {
                $percentageChangeMonth[$activity_id] = null;
            }
        }

        $activityVisitorCountsYear = ActualVisitors::join('bookings', 'actual_visitors.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2, 3])
            ->whereBetween('bookings.booking_date', [$startMonth, $endMonth])
            ->where('bookings.status', 2)
            ->select(
                'bookings.activity_id',
                DB::raw('SUM(actual_children_qty + actual_students_qty + actual_adults_qty + actual_kid_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty + actual_free_teachers_qty) as total_visitors')
            )
            ->groupBy('bookings.activity_id')
            ->pluck('total_visitors', 'bookings.activity_id');

        $activity1VisitorsYear = $activityVisitorCountsYear[1] ?? 0;
        $activity2VisitorsYear = $activityVisitorCountsYear[2] ?? 0;
        $activity3VisitorsYear = $activityVisitorCountsYear[3] ?? 0;

        if ($activity3VisitorsYear > 0) {
            $activity1VisitorsYear += $activity3VisitorsYear;
            $activity2VisitorsYear += $activity3VisitorsYear;
        }

        $totalVisitorsThisYear = [
            1 => $activity1VisitorsYear,
            2 => $activity2VisitorsYear
        ];

        $startMonthLastYear = $startMonth->copy()->subYear();
        $endMonthLastYear = $endMonth->copy()->subYear();

        $activityVisitorCountsLastYear = ActualVisitors::join('bookings', 'actual_visitors.booking_id', '=', 'bookings.booking_id')
            ->whereIn('bookings.activity_id', [1, 2, 3])
            ->whereBetween('bookings.booking_date', [$startMonthLastYear, $endMonthLastYear])
            ->where('bookings.status', 2)
            ->select(
                'bookings.activity_id',
                DB::raw('SUM(actual_children_qty + actual_students_qty + actual_adults_qty + actual_kid_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty + actual_free_teachers_qty) as total_visitors')
            )
            ->groupBy('bookings.activity_id')
            ->pluck('total_visitors', 'bookings.activity_id');

        $activity1VisitorsLastYear = $activityVisitorCountsLastYear[1] ?? 0;
        $activity2VisitorsLastYear = $activityVisitorCountsLastYear[2] ?? 0;
        $activity3VisitorsLastYear = $activityVisitorCountsLastYear[3] ?? 0;
        if ($activity3VisitorsLastYear > 0) {
            $activity1VisitorsLastYear += $activity3VisitorsLastYear;
            $activity2VisitorsLastYear += $activity3VisitorsLastYear;
        }

        $totalVisitorsLastYear = [
            1 => $activity1VisitorsLastYear,
            2 => $activity2VisitorsLastYear
        ];

        $percentageChangeYear = [];
        foreach ([1, 2] as $activityId) {
            $lastYear = $totalVisitorsLastYear[$activityId] ?? 0;
            $thisYear = $totalVisitorsThisYear[$activityId] ?? 0;

            if ($lastYear > 0) {
                $percentageChangeYear[$activityId] = (($thisYear - $lastYear) / $lastYear) * 100;
            } else {
                $percentageChangeYear[$activityId] = $thisYear > 0 ? 100 : 0;
            }
        }

        $totalVisitorsPerMonthThisYear = [];
        $currentDate = $startMonth->copy();
        while ($currentDate <= $endMonth) {
            $monthStart = $currentDate->copy()->startOfMonth()->format('Y-m-d');
            $monthEnd = $currentDate->copy()->endOfMonth()->format('Y-m-d');
            $monthLabel = $currentDate->copy()->translatedFormat('M') . ' ' . ($currentDate->year + 543);

            $totalVisitorsPerMonthThisYear[$monthLabel] = ActualVisitors::join('bookings', 'actual_visitors.booking_id', '=', 'bookings.booking_id')
                ->join('activities', 'bookings.activity_id', '=', 'activities.activity_id')
                ->whereBetween('bookings.booking_date', [$monthStart, $monthEnd])
                ->where('bookings.status', 2)
                ->where('activities.activity_type_id', 1)
                ->sum(DB::raw('actual_children_qty + actual_students_qty + actual_adults_qty + actual_kid_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty + actual_free_teachers_qty'));
            $currentDate->addMonth();
        }

        $yearlyRevenueGeneral = [];
        $currentDate = $startMonth->copy();
        while ($currentDate <= $endMonth) {
            $monthStart = $currentDate->copy()->startOfMonth()->format('Y-m-d');
            $monthEnd = $currentDate->copy()->endOfMonth()->format('Y-m-d');
            $monthLabel = $currentDate->copy()->translatedFormat('M') . ' ' . ($currentDate->year + 543);

            $yearlyRevenueGeneral[$monthLabel] = Bookings::whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
                ->join('activities', 'bookings.activity_id', '=', 'activities.activity_id')
                ->join('actual_visitors', 'bookings.booking_id', '=', 'actual_visitors.booking_id')
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->whereIn('bookings.status', [2])
                ->sum(DB::raw(
                    '(actual_children_qty * children_price) + (actual_students_qty * student_price) + 
                 (actual_adults_qty * adult_price) + (actual_kid_qty * kid_price) + (actual_disabled_qty * disabled_price) + 
                 (actual_elderly_qty * elderly_price) + (actual_monk_qty * monk_price)'
                ));
            $currentDate->addMonth();
        }
        $yearlyRevenueActivity = [];
        $currentDate = $startMonth->copy();
        while ($currentDate <= $endMonth) {
            $monthStart = $currentDate->copy()->startOfMonth()->format('Y-m-d');
            $monthEnd = $currentDate->copy()->endOfMonth()->format('Y-m-d');
            $monthLabel = $currentDate->copy()->translatedFormat('M') . ' ' . ($currentDate->year + 543);

            $yearlyRevenueActivity[$monthLabel] = Bookings::whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
                ->join('activities', 'bookings.activity_id', '=', 'activities.activity_id')
                ->join('actual_visitors', 'bookings.booking_id', '=', 'actual_visitors.booking_id')
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->whereIn('bookings.status', [2])
                ->sum(DB::raw(
                    '(actual_children_qty * children_price) + (actual_students_qty * student_price) + 
                     (actual_adults_qty * adult_price) + (actual_disabled_qty * disabled_price) +
                     (actual_kid_qty * kid_price) + (actual_elderly_qty * elderly_price) + (actual_monk_qty * monk_price)'
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
                ->where(function ($query) {
                    $query->where('note', '!=', 'วอคอิน')
                        ->orWhereNull('note');
                })
                ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty + monk_qty'));
        }

        $totalWalkinBooked = [];
        foreach ($activities as $activity) {
            $totalWalkinBooked[$activity->activity_id] = ActualVisitors::whereHas('booking', function ($query) use ($activity, $startMonth, $endMonth) {
                $query->where('activity_id', $activity->activity_id)
                    ->whereBetween('booking_date', [$startMonth, $endMonth])
                    ->where('note', 'วอคอิน')
                    ->whereHas('activity', function ($subQuery) {
                        $subQuery->where('activity_type_id', 1);
                    });
            })
                ->sum(DB::raw('actual_children_qty + actual_students_qty + actual_adults_qty + actual_kid_qty + actual_disabled_qty + actual_elderly_qty + actual_monk_qty + actual_free_teachers_qty'));
        }
        $totalSpecialActivity = Activity::with(['activityType', 'bookings.actualVisitors'])
            ->where('activity_type_id', 2)
            ->select('activity_id', 'activity_name', 'target_yearly_count')
            ->get()
            ->map(function ($activity) use ($startMonth, $endMonth) {
                $totalVisitors = $activity->bookings()
                    ->whereBetween('booking_date', [$startMonth, $endMonth])
                    ->with('actualVisitors')
                    ->get()
                    ->sum(function ($booking) {
                        return $booking->actualVisitors 
                        ? $booking->actualVisitors->actual_children_qty +
                          $booking->actualVisitors->actual_students_qty +
                          $booking->actualVisitors->actual_adults_qty +
                          $booking->actualVisitors->actual_kid_qty +
                          $booking->actualVisitors->actual_disabled_qty +
                          $booking->actualVisitors->actual_elderly_qty +
                          $booking->actualVisitors->actual_monk_qty +
                          $booking->actualVisitors->actual_free_teachers_qty
                        : 0;
                });

                $totalBookings = $activity->bookings()
                    ->whereBetween('booking_date', [$startMonth, $endMonth])
                    ->distinct('booking_id')
                    ->where('status', 2)
                    ->count();

                return (object) [
                    'activity_id' => $activity->activity_id,
                    'activity_name' => $activity->activity_name,
                    'target_yearly_count' => $activity->target_yearly_count,
                    'total_visitors' => $totalVisitors,
                    'total_bookings' => $totalBookings,
                ];
            });
        $visitorStats = Bookings::join('actual_visitors', 'bookings.booking_id', '=', 'actual_visitors.booking_id')
            ->join('activities', 'bookings.activity_id', '=', 'activities.activity_id')
            ->join('status_changes', function ($join) {
                $join->on('bookings.booking_id', '=', 'status_changes.booking_id')
                     ->where('status_changes.new_status', 2);
            })
            ->selectRaw('
                SUM(actual_children_qty) as children_qty, 
                SUM(actual_students_qty) as students_qty, 
                SUM(actual_adults_qty) as adults_qty,
                SUM(actual_kid_qty) as kid_qty, 
                SUM(actual_disabled_qty) as disabled_qty, 
                SUM(actual_elderly_qty) as elderly_qty, 
                SUM(actual_monk_qty) as monk_qty,
                SUM(actual_free_teachers_qty) as free_teachers_qty
            ')
            ->whereBetween('booking_date', [$startMonth, $endMonth])
            ->where('activities.activity_type_id', 1)
            ->first();

        return view('admin.dashboard', compact(
            'activities',
            'totalVisitorsToday',
            'percentageChangeToday',
            'totalVisitorsThisWeek',
            'percentageChangeThisWeek',
            'totalVisitorsThisMonth',
            'percentageChangeMonth',
            'totalVisitorsThisYear',
            'percentageChangeYear',
            'totalVisitorsPerMonthThisYear',
            'yearlyRevenueGeneral',
            'yearlyRevenueActivity',
            'totalVisitorsBooked',
            'totalWalkinBooked',
            'totalSpecialActivity',
            'visitorStats',
            'startMonth',
            'endMonth',
            'startMonthThai',
            'endMonthThai',
        ));
    }
}
