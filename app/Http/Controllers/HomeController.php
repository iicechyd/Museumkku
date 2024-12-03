<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function showWelcome()
    {
        return view('welcome');
    }

    public function showFormBookings()
    {
        return view('form_bookings');
    }

    public function showPreview()
    {
        return view('preview');
    }
    public function showCalendar()
    {
        return view('calendar');
    }
}
