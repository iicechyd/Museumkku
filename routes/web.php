<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingActivityController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\SubActivityController;
use App\Http\Controllers\TimeslotsController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DocumentController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminCalendarController;
use App\Http\Controllers\AuthController;

//Middleware routes
Auth::routes();
Route::middleware([RoleMiddleware::class . ':Super Admin'])->group(function () {
    Route::get('/super_admin/all_users', [SuperAdminController::class, 'showAllUsers'])->name('showAllUsers');
    Route::get('/superadmin/logs', [SuperAdminController::class, 'showUserLogs'])->name('superadmin.logs');
    Route::post('/approve_user/{user_id}', [SuperAdminController::class, 'approveUsers'])->name('superadmin.approve_users');
    Route::delete('/superadmin/users/{user_id}', [SuperAdminController::class, 'deleteUser'])
        ->name('superadmin.delete_user');
});
Route::middleware([RoleMiddleware::class . ':Admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'showDashboard'])->name('showDashboard');
    Route::get('/admin/activity_list', [ActivityController::class, 'showListActivity']);
    Route::get('/admin/admin_calendar', [HomeController::class, 'showAdminCalendar'])->name('calendar.showAdminCalendar');
    Route::get('/admin/history', [BookingController::class, 'showHistory'])->name('booking.history.all');
    // Route ตรวจการจองทั่วไป
    Route::get('/admin/manage_bookings/general', [BookingController::class, 'showTodayBookings'])->name('today_bookings.general');
    Route::get('/admin/request_bookings/general', [BookingController::class, 'showBookings'])->name('request_bookings.general');
    Route::get('/admin/request_letter/general', [BookingController::class, 'showRequestLetter'])->name('request_letter');
    Route::get('/admin/approved_bookings/general', [BookingController::class, 'showApproved'])->name('approved_bookings.general');
    Route::get('/admin/except_cases_bookings/general', [BookingController::class, 'showExcept'])->name('except_bookings.general');
    // Route ตรวจการจองกิจกรรม
    Route::get('/admin/manage_bookings/activity', [BookingActivityController::class, 'showTodayBookings'])->name('today_bookings.activity');
    Route::get('/admin/request_bookings/activity', [BookingActivityController::class, 'showBookingsActivity'])->name('request_bookings.activity');
    Route::get('/admin/approved_bookings/activity', [BookingActivityController::class, 'showApprovedActivity'])->name('approved_bookings.activity');
    Route::get('/admin/except_cases_bookings/activity', [BookingActivityController::class, 'showExceptActivity'])->name('except_bookings.activity');
    //Route ปิดรอบการเข้าชม
    Route::delete('admin/closed-dates/{id}', [TimeslotsController::class, 'deleteClosedDate'])->name('admin.deleteClosedDate');
    Route::get('/admin/manage-closed-dates', [TimeslotsController::class, 'showClosedDates'])->name('admin.manageClosedDates');
    Route::post('/admin/manage-closed-dates', [TimeslotsController::class, 'saveClosedDates'])->name('admin.saveClosedDates');
    Route::post('/admin/getTmsActivity', [TimeslotsController::class, 'getTimeslotsByActivity'])->name('admin.getTimeslots');
    //Route หลักสูตร
    Route::get('/admin/subactivity_list', [SubActivityController::class, 'showSubActivities'])->name('admin.subactivities');
    Route::post('/admin/subactivities/store', [SubActivityController::class, 'storeSubActivity'])->name('admin.storeSubActivity');
    Route::post('/admin/toggle-subactivity-status/{subActivityId}', [SubActivityController::class, 'toggleSubactivityStatus']);
    Route::post('/admin/update-max-subactivities', [SubActivityController::class, 'updateMaxSubactivities'])->name('admin.updateMaxSubactivities');
    Route::delete('/admin/deleteSubActivity/{id}', [SubActivityController::class, 'delete'])->name('admin.deleteSubActivity');
    Route::put('/admin/sub-activity/{subActivityId}', [SubActivityController::class, 'update'])->name('admin.updateSubActivity');
     //Route กิจกรรม
    Route::post('/InsertActivity', [ActivityController::class, 'InsertActivity'])->name('insert.activity');
    Route::get('delete/{activity_id}', [ActivityController::class, 'delete'])->name('delete');
    Route::post('/UpdateActivity', [ActivityController::class, 'updateActivity'])->name('updateActivity');
    Route::get('/getActivityPrice/{activity_id}', [ActivityController::class, 'getActivityPrice']);
    Route::get('activity/toggle-status/{id}', [ActivityController::class, 'toggleStatus'])->name('toggle.status');
    Route::post('activity/toggle-status/{id}', [ActivityController::class, 'toggleStatus'])->name('toggle.status');
    Route::delete('/admin/activity_images/{image_id}', [ActivityController::class, 'deleteImage'])->name('deleteImage');
    Route::post('/add-target', [ActivityController::class, 'addTarget']);
    //Route รอบการเข้าชม
    Route::get('/tms_list', [TimeslotsController::class, 'showTimeslots'])->name('showTimeslots');
    Route::post('/InsertTms', [TimeslotsController::class, 'InsertTimeslots'])->name('InsertTimeslots');
    Route::delete('/delete/timeslots/{id}', [TimeslotsController::class, 'delete'])->name('timeslots.delete');
    Route::put('/update/tms/{id}', [TimeslotsController::class, 'update'])->name('timeslots.update');
    Route::get('/toggle-status/{id}', [TimeslotsController::class, 'toggleStatus'])->name('toggle.status');
    Route::post('/toggle-status/{id}', [TimeslotsController::class, 'toggleStatus'])->name('toggle.status');
});
Route::middleware([RoleMiddleware::class . ':Executive'])->group(function () {
    Route::get('/executive/dashboard', [DashboardController::class, 'showDashboard'])->name('showDashboard');
});
//Route อัปเดตสถานะการจอง อัปโหลดไฟล์ขอความอนุเคราะห์
Route::post('/bookings/{booking_id}/updateStatus', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
Route::get('/documents/upload/{booking_id}', [DocumentController::class, 'showUploadForm'])->name('documents.upload');
Route::post('/documents/upload/{booking_id}', [DocumentController::class, 'uploadDocument'])->name('documents.store');
Route::put('/documents/{document_id}', [DocumentController::class, 'update'])->name('documents.update');
//ดึงeventที่ได้รับการจองมาแสดงบนปฏิทิน
Route::get('/calendar', [HomeController::class, 'showCalendar'])->name('calendar.showCalendar');
Route::get('/calendar/events', [CalendarController::class, 'getEvents'])->name('calendar.events');
Route::get('/admin_calendar/events', [AdminCalendarController::class, 'getEvents'])->name('admin_calendar.events');
Route::get('/calendar/timeslots/{date}', [CalendarController::class, 'getTimeslotsForDate']);
//Rpute เข้าสู่ระบบ ล็อคเอ้าท์
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
//Route สำหรับผู้เข้าชม หน้าเพจต่างๆ
Route::get('/', [HomeController::class, 'showWelcome'])->name('showWelcome');
Route::get('/preview', [HomeController::class, 'showPreview'])->name('showPreview');
Route::get('/preview_activity', [ActivityController::class, 'previewActivity'])->name('preview_activity');
Route::get('/preview_general', [ActivityController::class, 'previewGeneral'])->name('preview_general');
Route::get('/activity/{activity_id}', [ActivityController::class, 'showDetail'])->name('activity_detail');
Route::get('/form_bookings/activity/{activity_id}', [BookingController::class, 'showBookingForm'])->name('form_bookings.activity');
Route::post('/submitBooking', [BookingController::class, 'InsertBooking'])->name('InsertBooking');
Route::get('/available-tms/{activity_id}/{date}', [TimeslotsController::class, 'getAvailableTimeslots']);
Route::post('change_status/{booking_id}', [BookingController::class, 'changeStatus'])->name('changeStatus');
Route::get('/checkBookingStatus', [BookingController::class, 'checkBookingStatus'])->name('checkBookingStatus');
Route::post('/checkBookingStatus', [BookingController::class, 'searchBookingByEmail'])->name('searchBookingByEmail');
//Route อัปโหลดไฟล์
Route::get('/documents/upload/{booking_id}', [DocumentController::class, 'showUploadForm'])->name('documents.upload');
Route::post('/documents/upload/{booking_id}', [DocumentController::class, 'uploadDocument'])->name('documents.store');
Route::delete('delete/documents/{document_id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
//Route ยืนยันอีเมล
Route::get('/guest_verify', [AuthController::class, 'showGuestVerify'])->name('guest.verify');
Route::post('/api/send-verification-link', [AuthController::class, 'sendVerificationLink'])->name('sendVerificationLink');
Route::get('/verify-link/{token}', [AuthController::class, 'verifyLink'])->name('verifyLink');
Route::get('/waiting-for-verification', [AuthController::class, 'waitingForVerification'])->name('waiting_for_verification');
Route::get('/check-verification/{email}', [AuthController::class, 'checkVerification'])->name('checkVerification');
//Route แก้ไขแบบฟอร์ม
Route::get('/booking/details/{booking_id}', [BookingController::class, 'showDetails'])->name('bookings.details')->middleware('signed');
Route::get('/bookings/edit/{booking_id}', [BookingController::class, 'showBookingEdit'])->name('bookings.edit')->middleware('signed');
Route::get('/admin/edit-booking/{booking_id}', [BookingController::class, 'showBookingAdminEdit'])->name('admin.edit_booking')->middleware('signed');
Route::put('/bookings/update/{booking_id}', [BookingController::class, 'updateBooking'])->name('bookings.update');
Route::get('/bookings/cancel/{booking_id}', [BookingController::class, 'showCancel'])->name('bookings.cancel')->middleware('signed');