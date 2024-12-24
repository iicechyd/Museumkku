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

        return view('admin.dashboard', compact('activities', 'totalVisitors'));
    }
}
