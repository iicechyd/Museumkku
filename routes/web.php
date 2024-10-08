<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingActivityController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TimeslotController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/', [ActivityController::class, 'index'])->name('index');

Route::get('/form_bookings', function () {
    return view('form_bookings');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

// Route::get('/calendar', function () {
//     return view('calendar');
// });

Route::get('/calendar', [BookingController::class, 'showCalendar'])->name('calendar');
Route::get('/calendar/bookings', [BookingController::class, 'getApprovedBookings']);
Route::get('/', [ActivityController::class, 'index']);
Route::get('/preview_activity', [ActivityController::class, 'previewActivity'])->name('preview_activity');
Route::get('/admin/activity_list', [ActivityController::class, 'showListActivity']);


// Route สำหรับฝั่งแอดมิน การจองทั่วไป
Route::get('/admin/request_bookings/general', [BookingController::class, 'showBookings']);
Route::get('/admin/approved_bookings/general', [BookingController::class, 'showApproved']);
Route::get('/admin/except_cases_bookings/general', [BookingController::class, 'showExcept']);

// Route สำหรับฝั่งแอดมิน การจองกิจกรรม
Route::get('/admin/request_bookings/activity', [BookingActivityController::class, 'showBookingsActivity']);
Route::get('/admin/approved_bookings/activity', [BookingActivityController::class, 'showApprovedActivity']);
Route::get('/admin/except_cases_bookings/activity', [BookingActivityController::class, 'showExceptActivity']);


// Route สำหรับจองเข้าชมทั่วไป
Route::get('/form_bookings/general', [BookingController::class, 'showGeneralBookingForm']);

// Route สำหรับจองเข้าร่วมกิจกรรมที่ดึงเฉพาะ activity_type_id = 2
Route::get('/form_bookings/activity', [BookingController::class, 'showActivityBookingForm']);

//ส่งการจองไปยัง Booking
Route::post('/InsertBooking', [BookingController::class, 'InsertBooking'])->name('InsertBooking');
Route::post('change_status/{booking_id}', [BookingController::class, 'changeStatus'])->name('changeStatus');
Route::get('/timeslots', [TimeslotController::class, 'getTimeslots'])->name('timeslots');
Route::get('/form_bookings', [ActivityController::class, 'createBookingForm'])->name('booking.form');

//แก้ไขตาราง Activity
Route::post('/InsertActivity', [ActivityController::class, 'InsertActivity'])->name('insert.activity');
Route::get('delete/{activity_id}', [ActivityController::class, 'delete'])->name('delete');
Route::post('/UpdateActivity', [ActivityController::class, 'updateActivity'])->name('updateActivity');
Route::get('/getActivityPrice/{activity_id}', [ActivityController::class, 'getActivityPrice']);

//แก้ไข Timeslots 
Route::get('/admin/timeslots_list', [TimeslotController::class, 'showTimeslots'])->name('showTimeslots');
Route::post('/InsertTimeslots', [TimeslotController::class, 'InsertTimeslots'])->name('InsertTimeslots');
Route::delete('/timeslots/{timeslots_id}', [TimeslotController::class, 'destroy'])->name('timeslots.destroy');
Route::put('timeslots/{id}', [TimeslotController::class, 'update'])->name('timeslots.update');

//Middleware routes
Auth::routes();
Route::group(['middleware' => 'auth'], function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });
    Route::get('/admin/request_bookings', [BookingController::class, 'showBookings'])->name('request_bookings');
    Route::get('/admin/approved_bookings', [BookingController::class, 'showApproved'])->name('approved_bookings');
    Route::get('/admin/except_cases_bookings', [BookingController::class, 'showExcept'])->name('except_bookings');
    Route::post('/admin/request_bookings/update-status/{booking_id}', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
});
