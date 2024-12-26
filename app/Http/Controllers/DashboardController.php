<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookings;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $activities = Activity::all();
        
        $totalVisitors = [];
        foreach ($activities as $activity) {
            $totalVisitors[$activity->activity_id] = Bookings::where('activity_id', $activity->activity_id)
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));
        }
    
        $specialActivities = DB::table('activities')
        ->join('activity_types', 'activities.activity_type_id', '=', 'activity_types.activity_type_id')
        ->leftJoin('bookings', 'activities.activity_id', '=', 'bookings.activity_id')
        ->select(
            'activities.activity_name',
            DB::raw('
                SUM(
                    COALESCE(bookings.children_qty, 0) +
                    COALESCE(bookings.students_qty, 0) +
                    COALESCE(bookings.adults_qty, 0) +
                    COALESCE(bookings.disabled_qty, 0) +
                    COALESCE(bookings.elderly_qty, 0) +
                    COALESCE(bookings.monk_qty, 0)
                ) as total_visitors
            '),
            DB::raw('
                SUM(
                    COALESCE(bookings.children_qty, 0) * COALESCE(activities.children_price, 0) +
                    COALESCE(bookings.students_qty, 0) * COALESCE(activities.student_price, 0) +
                    COALESCE(bookings.adults_qty, 0) * COALESCE(activities.adult_price, 0) +
                    COALESCE(bookings.disabled_qty, 0) * COALESCE(activities.disabled_price, 0) +
                    COALESCE(bookings.elderly_qty, 0) * COALESCE(activities.elderly_price, 0) +
                    COALESCE(bookings.monk_qty, 0) * COALESCE(activities.monk_price, 0)
                ) as total_price
            ')
        )
        ->where('activity_types.activity_type_id', '=', 2)
        ->groupBy('activities.activity_id', 'activities.activity_name')
        ->get();

            // ดึงข้อมูลจำนวนผู้เข้าชมทั้งหมด
        $visitorStats = Bookings::selectRaw('
        SUM(children_qty) as children_qty, 
        SUM(students_qty) as students_qty, 
        SUM(adults_qty) as adults_qty, 
        SUM(disabled_qty) as disabled_qty, 
        SUM(elderly_qty) as elderly_qty, 
        SUM(monk_qty) as monk_qty
    ')
    ->first();

        return view('admin.dashboard', compact('activities', 'totalVisitors', 'specialActivities','visitorStats'));
    }    
}
