<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingActivityController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TimeslotController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExecutiveController;
use App\Http\Middleware\RoleMiddleware;

//Middleware routes
Auth::routes();
Route::middleware([RoleMiddleware::class . ':Super Admin'])->group(function () {
    Route::get('/all_users', [SuperAdminController::class, 'showAllUsers'])->name('showAllUsers');
    Route::get('/super_admin/dashboard', [SuperAdminController::class, 'showDashboard'])->name('showDashboard');
});
Route::middleware([RoleMiddleware::class . ':Admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'showDashboard'])->name('showDashboard');
    Route::get('/admin/activity_list', [ActivityController::class, 'showListActivity']);
    // Route ตรวจการจองทั่วไป
    Route::get('/admin/request_bookings/general', [BookingController::class, 'showBookings'])->name('request_bookings.general');
    Route::get('/admin/approved_bookings/general', [BookingController::class, 'showApproved'])->name('approved_bookings');
    Route::get('/admin/except_cases_bookings/general', [BookingController::class, 'showExcept'])->name('except_bookings');
    // Route ตรวจการจองกิจกรรม
    Route::get('/admin/request_bookings/activity', [BookingActivityController::class, 'showBookingsActivity'])->name('request_bookings.activity');
    Route::get('/admin/approved_bookings/activity', [BookingActivityController::class, 'showApprovedActivity'])->name('approved_bookings');
    Route::get('/admin/except_cases_bookings/activity', [BookingActivityController::class, 'showExceptActivity'])->name('except_bookings');    
});

//Route หน้าเพจต่างๆ
Route::get('/', [HomeController::class, 'showWelcome'])->name('showWelcome');
Route::get('/form_bookings', [HomeController::class, 'showFormBookings'])->name('showFormBookings');
Route::get('/preview', [HomeController::class, 'showPreview'])->name('showPreview');
Route::get('/preview_activity', [ActivityController::class, 'previewActivity'])->name('preview_activity');
Route::get('/preview_general', [ActivityController::class, 'previewGeneral'])->name('preview_general');
Route::get('/activity/{activity_id}', [ActivityController::class, 'showDetail'])->name('activity_detail');
Route::get('/calendar', [HomeController::class, 'showCalendar'])->name('calendar.showCalendar');

//ดึงeventที่ได้รับการจองมาแสดงบนปฏิทิน
Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
Route::get('/bookings/{booking}', [CalendarController::class, 'show'])->name('bookings.show');

 //ฟอร์มสำหรับจองกิจกรรมของแต่ละกิจกรรม
 Route::get('/form_bookings/activity/{activity_id}', [BookingActivityController::class, 'showActivityBookingForm'])->name('form_bookings.activity');

 //แสดงสถานะการจองให้ผู้จองเข้าชม
Route::get('/showBookingStatus', [BookingController::class, 'showBookingStatus'])->name('showBookingStatus');
Route::get('/checkBookingStatus', [BookingController::class, 'checkBookingStatus'])->name('checkBookingStatus');
Route::post('/checkBookingStatus', [BookingController::class, 'searchBookingByEmail'])->name('searchBookingByEmail');

//Route ส่งค่าต่างๆ
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/approve_user/{user_id}', [SuperAdminController::class, 'approveUsers'])->name('superadmin.approve_users');
Route::post('/InsertBooking', [BookingController::class, 'InsertBooking'])->name('InsertBooking');
Route::post('change_status/{booking_id}', [BookingController::class, 'changeStatus'])->name('changeStatus');
Route::get('/timeslots/{activity_id}', [TimeslotController::class, 'getTimeslots']);
Route::get('/form_bookings', [ActivityController::class, 'createBookingForm'])->name('booking.form');

//UpdateStutusBookings
Route::post('/bookings/{booking_id}/updateStatus', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');

//แก้ไขตาราง Activity
Route::post('/InsertActivity', [ActivityController::class, 'InsertActivity'])->name('insert.activity');
Route::get('delete/{activity_id}', [ActivityController::class, 'delete'])->name('delete');
Route::post('/UpdateActivity', [ActivityController::class, 'updateActivity'])->name('updateActivity');
Route::get('/getActivityPrice/{activity_id}', [ActivityController::class, 'getActivityPrice']);
Route::get('activity/toggle-status/{id}', [ActivityController::class, 'toggleStatus'])->name('toggle.status');
Route::post('activity/toggle-status/{id}', [ActivityController::class, 'toggleStatus'])->name('toggle.status');

//แก้ไข Timeslots 
Route::get('/admin/timeslots_list', [TimeslotController::class, 'showTimeslots'])->name('showTimeslots');
Route::post('/InsertTimeslots', [TimeslotController::class, 'InsertTimeslots'])->name('InsertTimeslots');
Route::delete('/timeslots/{timeslots_id}', [TimeslotController::class, 'destroy'])->name('timeslots.destroy');
Route::put('timeslots/{id}', [TimeslotController::class, 'update'])->name('timeslots.update');
Route::get('/toggle-status/{id}', [TimeslotController::class, 'toggleStatus'])->name('toggle.status');
Route::post('/toggle-status/{id}', [TimeslotController::class, 'toggleStatus'])->name('toggle.status');