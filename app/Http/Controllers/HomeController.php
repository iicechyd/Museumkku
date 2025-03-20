<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function showWelcome()
    {
        session()->forget('verification_email');
        return view('welcome');
    }

    public function showPreview()
    {
        session()->forget('verification_email');
        return view('preview');
    }
    public function showCalendar()
    {
        session()->forget('verification_email');
        return view('calendar');
    }
    public function showAdminCalendar()
    {
        return view('admin.admin_calendar');
    }
}
