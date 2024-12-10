<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bookings;
use App\Models\Timeslots;
use App\Models\Activity;

class BookingActivityController extends Controller
{
    function showBookingsActivity()
    {
        $requestBookings = Bookings::with(['activity', 'timeslot', 'visitor', 'institute'])
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
            ->where('status', 0)
            ->paginate(5);

        foreach ($requestBookings as $item) {
            $totalApproved = 0;

            // ตรวจสอบว่า activity มี timeslot หรือไม่
            if ($item->timeslot) {
                // หากมี timeslot คำนวณจำนวนคนที่จองใน timeslot นั้น
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('timeslots_id', $item->timeslot->timeslots_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty'));
            } else {
                // หากไม่มี timeslot คำนวณจำนวนคนที่จองในวันเดียวกันและกิจกรรมเดียวกัน
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('status', 1) // เฉพาะการจองที่อนุมัติแล้ว
                    ->sum(DB::raw('children_qty + students_qty + adults_qty'));
            }

            // ดึงค่าความจุสูงสุดจาก activity
            $maxCapacity = $item->activity->max_capacity;

            // คำนวณความจุคงเหลือ
            if ($maxCapacity === null) {
                $item->remaining_capacity = 'ไม่จำกัดจำนวนคน';
            } else {
                // คำนวณความจุคงเหลือโดยลบจากการจองที่อนุมัติแล้ว
                $item->remaining_capacity = $maxCapacity - $totalApproved;
            }

            // คำนวณราคาทั้งหมด
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;

            // คำนวณราคาทั้งหมด
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;

            // คำนวณจำนวนผู้เข้าชมทั้งหมด
            $item->totalVisitors = $item->children_qty + $item->students_qty + $item->adults_qty;
        }
        return view('admin.activityRequest.request_bookings', compact('requestBookings'));
    }

    function showApprovedActivity()
    {
        $approvedBookings = Bookings::with('activity', 'timeslot')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
            ->where('status', 1)
            ->paginate(5);

        foreach ($approvedBookings as $item) {
            // ตรวจสอบจำนวนการจองที่มีการอนุมัติแล้วในวันเดียวกัน (booking_date)
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('activity_id', $item->activity_id) // ตรวจสอบกิจกรรมเดียวกัน
                ->where('status', 1) // นับเฉพาะการจองที่อนุมัติแล้ว
                ->sum(DB::raw('children_qty + students_qty + adults_qty'));

            // ตรวจสอบว่า item นี้มี timeslot หรือไม่
            if ($item->timeslot && $item->timeslot->timeslots_id) {
                // หากมี timeslot ให้เพิ่มเงื่อนไขการตรวจสอบ timeslots_id
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('timeslots_id', $item->timeslot->timeslots_id) // คำนวณตาม timeslot
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty'));
            }

            // คำนวณ remaining capacity โดยลบจำนวนการจองออกจาก max capacity
            if ($item->activity->max_capacity !== null) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
            } else {
                // ถ้าไม่มี max capacity แสดงว่าไม่จำกัดจำนวนคน
                $item->remaining_capacity = 'ไม่จำกัดจำนวนคน';
            }

            // คำนวณราคาทั้งหมด
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;

            // คำนวณจำนวนคนทั้งหมด
            $item->totalVisitors = $item->children_qty + $item->students_qty + $item->adults_qty;
        }

        return view('admin.activityRequest.approved_bookings', compact('approvedBookings'));
    }

    function showExceptActivity()
    {
        $exceptBookings = Bookings::with('activity', 'timeslot')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 2);
            })
            ->where('status', 2)
            ->paginate(5);

        foreach ($exceptBookings as $item) {
            $totalApproved = 0;
            if ($item->timeslot) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('timeslots_id', $item->timeslot->timeslots_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty'));
            }
            // ตรวจสอบว่า activity มีค่า ก่อนเข้าถึง max_capacity
            if ($item->activity) {
                // คำนวณความจุคงเหลือ โดยลบจากยอดคนที่อนุมัติไปแล้ว
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;

                // เพิ่มยอดคนที่จองในสถานะนี้ (status = 2) กลับไปในยอดความจุคงเหลือ
                $item->remaining_capacity += $item->children_qty + $item->students_qty + $item->adults_qty;
            } else {
                // กรณีไม่มีข้อมูล activity ให้แสดงค่าเริ่มต้น เช่น 'N/A' หรือค่าที่เหมาะสมอื่นๆ
                $item->remaining_capacity = 'N/A';
            }
            // Calculate total price (for reference or auditing purposes)
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;

            // Calculate total price
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;

            // Calculate total visitors
            $item->totalVisitors = $item->children_qty + $item->students_qty + $item->adults_qty;
        }

        return view('admin.activityRequest.except_cases_bookings', compact('exceptBookings'));
    }

    public function deleteTimeslots($id)
    {
        $timeslot = Timeslots::findOrFail($id);
        $timeslot->delete();

        return redirect()->back()->with('success', 'ลบรอบการเข้าชมสำเร็จ');
    }
}
