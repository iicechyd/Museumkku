<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingActivityController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TimeslotController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\ExecutiveController;
use App\Http\Middleware\RoleMiddleware;

Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/pending_users', [SuperAdminController::class, 'showPendingUsers'])->name('pending_users')->middleware('TypeSuperAdmin');
Route::post('/approve_user/{user_id}', [SuperAdminController::class, 'approveUsers'])->name('superadmin.approve_users')->middleware('TypeSuperAdmin');
Route::get('/all_users', [SuperAdminController::class, 'showAllUsers'])->name('all_users')->middleware('TypeSuperAdmin');
Route::get('/super_admin/dashboard', function () {
    return view('superadmin.dashboard');
});


Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', [ActivityController::class, 'index'])->name('index');

Route::get('/activity/{activity_id}', [ActivityController::class, 'showDetail'])->name('activity_detail');


Route::get('/form_bookings', function () {
    return view('form_bookings');
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

Route::get('/toggle-status/{id}', [ActivityController::class, 'toggleStatus'])->name('toggle.status');
Route::post('/toggle-status/{id}', [ActivityController::class, 'toggleStatus'])->name('toggle.status');

//UpdateStutus
Route::post('/bookings/{booking_id}/updateStatus', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');

//แก้ไข Timeslots 
Route::get('/admin/timeslots_list', [TimeslotController::class, 'showTimeslots'])->name('showTimeslots');
Route::post('/InsertTimeslots', [TimeslotController::class, 'InsertTimeslots'])->name('InsertTimeslots');
Route::delete('/timeslots/{timeslots_id}', [TimeslotController::class, 'destroy'])->name('timeslots.destroy');
Route::put('timeslots/{id}', [TimeslotController::class, 'update'])->name('timeslots.update');

Route::get('/toggle-status/{id}', [TimeslotController::class, 'toggleStatus'])->name('toggle.status');
Route::post('/toggle-status/{id}', [TimeslotController::class, 'toggleStatus'])->name('toggle.status');

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
