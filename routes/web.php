<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingActivityController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TimeslotController;
use App\Http\Controllers\CalendarController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', [ActivityController::class, 'index'])->name('index');

Route::get('/activity/{activity_id}', [ActivityController::class, 'showDetail'])->name('activity_detail');


Route::get('/form_bookings', function () {
    return view('form_bookings');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});


Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
Route::get('/bookings/{booking}', [CalendarController::class, 'show'])->name('bookings.show');


Route::get('/', [ActivityController::class, 'index']);
Route::get('/preview', function () {
    return view('preview');
});

Route::get('/preview_activity', [ActivityController::class, 'previewActivity'])->name('preview_activity');
Route::get('/preview_general', [ActivityController::class, 'previewGeneral'])->name('preview_general');
Route::get('/admin/activity_list', [ActivityController::class, 'showListActivity']);


// Route สำหรับฝั่งแอดมิน การจองทั่วไป
// Route::get('/admin/request_bookings/general', [BookingController::class, 'showBookings']);
Route::get('/admin/approved_bookings/general', [BookingController::class, 'showApproved']);
Route::get('/admin/except_cases_bookings/general', [BookingController::class, 'showExcept']);

// Route สำหรับฝั่งแอดมิน การจองกิจกรรม
// Route::get('/admin/request_bookings/activity', [BookingActivityController::class, 'showActivityBookings']);
Route::get('/admin/approved_bookings/activity', [BookingActivityController::class, 'showApprovedActivity']);
Route::get('/admin/except_cases_bookings/activity', [BookingActivityController::class, 'showExceptActivity']);


// Route สำหรับจองเข้าชมทั่วไป
Route::get('/form_bookings/general', [BookingController::class, 'showGeneralBookingForm']);

// Route สำหรับจองเข้าร่วมกิจกรรมที่ดึงเฉพาะ activity_type_id = 2
// Route::get('/form_bookings/activity', [BookingController::class, 'showActivityBookingForm']);
Route::get('/form_bookings/activity/{activity_id}', [BookingActivityController::class, 'showActivityBookingForm'])->name('form_bookings.activity');

//ส่งการจองไปยัง Booking
Route::post('/InsertBooking', [BookingController::class, 'InsertBooking'])->name('InsertBooking');
Route::post('change_status/{booking_id}', [BookingController::class, 'changeStatus'])->name('changeStatus');
// Route::get('/timeslots', [TimeslotController::class, 'getTimeslots'])->name('timeslots');
Route::get('/timeslots/{activity_id}', [TimeslotController::class, 'getTimeslots']);

Route::get('/form_bookings', [ActivityController::class, 'createBookingForm'])->name('booking.form');

//แก้ไขตาราง Activity
Route::post('/InsertActivity', [ActivityController::class, 'InsertActivity'])->name('insert.activity');
Route::get('delete/{activity_id}', [ActivityController::class, 'delete'])->name('delete');
Route::post('/UpdateActivity', [ActivityController::class, 'updateActivity'])->name('updateActivity');
Route::get('/getActivityPrice/{activity_id}', [ActivityController::class, 'getActivityPrice']);

//เพิ่มประเภทกิจกรรม
// Route::post('/insert-activity-type', [ActivityController::class, 'store'])->name('insert.activityType');

//UpdateStutus
Route::post('/bookings/{booking_id}/updateStatus', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');

//แก้ไข Timeslots 
Route::get('/admin/timeslots_list', [TimeslotController::class, 'showTimeslots'])->name('showTimeslots');
Route::post('/InsertTimeslots', [TimeslotController::class, 'InsertTimeslots'])->name('InsertTimeslots');
Route::delete('/timeslots/{timeslots_id}', [TimeslotController::class, 'destroy'])->name('timeslots.destroy');
Route::put('timeslots/{id}', [TimeslotController::class, 'update'])->name('timeslots.update');

Route::get('/showBookingStatus', [BookingController::class, 'showBookingStatus'])->name('showBookingStatus');
Route::get('/checkBookingStatus', [BookingController::class, 'checkBookingStatus'])->name('checkBookingStatus');
Route::post('/checkBookingStatus', [BookingController::class, 'searchBookingByEmail'])->name('searchBookingByEmail');

//Middleware routes
Auth::routes();
Route::group(['middleware' => 'auth'], function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });
    Route::get('/admin/request_bookings/general', [BookingController::class, 'showBookings'])->name('request_bookings.general');
    Route::get('/admin/approved_bookings/general', [BookingController::class, 'showApproved'])->name('approved_bookings');
    Route::get('/admin/except_cases_bookings/general', [BookingController::class, 'showExcept'])->name('except_bookings');
    Route::get('/admin/request_bookings/activity', [BookingActivityController::class, 'showBookingsActivity'])->name('request_bookings.activity');
    Route::get('/admin/approved_bookings/activity', [BookingActivityController::class, 'showApprovedActivity'])->name('approved_bookings');
    Route::get('/admin/except_cases_bookings/activity', [BookingActivityController::class, 'showExceptActivity'])->name('except_bookings');

    // Route::post('/admin/request_bookings/update-status/{booking_id}', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
});
