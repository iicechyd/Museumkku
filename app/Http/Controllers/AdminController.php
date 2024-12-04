<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timeslots;
use App\Models\Activity;


class AdminController extends Controller
{
    public function showDashboard()
    {
        return view('admin.dashboard');
    }
}
