<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bookings;
use App\Models\Timeslots;
use App\Models\Activity;
use App\Models\Institutes;
use App\Models\Visitors;
use App\Models\StatusChanges;

use Carbon\Carbon;

class BookingController extends Controller
{
    function showBookings()
    {
        $requestBookings = Bookings::with(['activity', 'timeslot', 'visitor', 'institute'])
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
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

        return view('admin.generalRequest.request_bookings', compact('requestBookings'));
    }

    function showApproved()
    {
        $approvedBookings = Bookings::with('activity', 'timeslot', 'visitor', 'institute')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
            ->where('status', 1)
            ->paginate(5);

        foreach ($approvedBookings as $item) {
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('activity_id', $item->activity_id) // ตรวจสอบกิจกรรมเดียวกัน
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty'));

            if ($item->timeslot && $item->timeslot->timeslots_id) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('timeslots_id', $item->timeslot->timeslots_id) // คำนวณตาม timeslot
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty'));
            }

            if ($item->activity->max_capacity !== null) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
            } else {
                $item->remaining_capacity = 'ไม่จำกัดจำนวนคน';
            }

            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;

            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;
            $item->totalVisitors = $item->children_qty + $item->students_qty + $item->adults_qty;
        }

        return view('admin.generalRequest.approved_bookings', compact('approvedBookings'));
    }

    function showExcept()
    {
        $exceptBookings = Bookings::with('activity', 'timeslot', 'visitor', 'institute')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
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
            if ($item->activity) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;

                $item->remaining_capacity += $item->children_qty + $item->students_qty + $item->adults_qty;
            } else {
                $item->remaining_capacity = 'N/A';
            }
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;

            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;
            $item->totalVisitors = $item->children_qty + $item->students_qty + $item->adults_qty;
        }
        return view('admin.generalRequest.except_cases_bookings', compact('exceptBookings'));
    }

    public function updateStatus(Request $request, $booking_id)
    {
        $request->validate([
            'status' => 'required|in:pending,approve,cancel',
        ]);

        $booking = Bookings::where('booking_id', $booking_id)->firstOrFail();

        $oldStatus = $booking->status;

        switch ($request->status) {
            case 'pending':
                $newStatus = 0;
                break;
            case 'approve':
                $newStatus = 1;
                break;
            case 'cancel':
                $newStatus = 2;
                break;
        }

        $booking->status = $newStatus;
        $booking->save();

        // หา status_change ของ booking_id เดิมที่มีอยู่ในฐานข้อมูล
        $statusChange = StatusChanges::where('booking_id', $booking_id)->first();

        if ($statusChange) {
            $statusChange->old_status = $oldStatus;
            $statusChange->new_status = $newStatus;
            $statusChange->comments = $request->input('comments', $statusChange->comments);
            $statusChange->changed_by = Auth::user()->name;
            $statusChange->save();
        } else {
            // ถ้าไม่พบ ให้สร้างรายการใหม่
            StatusChanges::create([
                'booking_id' => $booking->booking_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'comments' => $request->input('comments', null),
                'changed_by' => Auth::user()->name,
            ]);
        }
        return redirect()->back()->with('success', 'สถานะการจองถูกอัปเดตแล้ว');
    }

    public function showCalendar()
    {
        return view('calendar'); // ส่ง view ของปฏิทิน
    }

    function InsertBooking(Request $request)
    {
        $request->validate(
            [
                'fk_activity_id' => 'required|exists:activities,activity_id',
                'fk_timeslots_id' => [
                    'nullable',
                    'exists:timeslots,timeslots_id',
                    function ($attribute, $value, $fail) use ($request) {
                        $activity = Activity::with('activityType')->find($request->input('fk_activity_id'));

                        if (!$activity) {
                            $fail('ไม่พบกิจกรรมที่เลือกในฐานข้อมูล');
                        } elseif ($activity->activityType->activity_type_id == 1 && !$value) {
                            $fail('กรุณาเลือกรอบการเข้าชมสำหรับกิจกรรมประเภทนี้');
                        }
                    }
                ],
                'booking_date' => 'required',
                'instituteName' => 'required',
                'instituteAddress' => 'required',
                'province' => 'required',
                'district' => 'required',
                'subdistrict' => 'required',
                'zipcode' => [
                    'required',
                    'regex:/^[0-9]{5}$/'
                ],
                'visitorName' => 'required',
                'visitorEmail' => 'required|email',
                'tel' => [
                    'required',
                    'regex:/^[0-9]{10}$/'
                ],
                'children_qty' => 'nullable|integer|min:0',
                'students_qty' => 'nullable|integer|min:0',
                'adults_qty' => 'nullable|integer|min:0',
            ]
        );

        // ตรวจสอบ timeslot เมื่อ activity_type_id = 1
        $activity = Activity::find($request->input('fk_activity_id'));
        if ($activity && $activity->activityType->activity_type_id == 1) {
            if (is_null($request->input('fk_timeslots_id'))) {
                return back()->with('error', 'กรุณาเลือกรอบการเข้าชมสำหรับกิจกรรมประเภทนี้');
            }
        }

        $activity = Activity::find($request->input('fk_activity_id'));

        if ($request->filled('fk_timeslots_id')) {
            $timeslot = Timeslots::find($request->input('fk_timeslots_id'));

            // คำนวณจำนวนที่จองทั้งหมดสำหรับวันที่และช่วงเวลาที่เลือก (เฉพาะสถานะที่อนุมัติ)
            $totalBooked = Bookings::where('booking_date', $request->booking_date)
                ->where('timeslots_id', $timeslot->timeslots_id)
                ->where('status', 1) // สถานะอนุมัติเท่านั้น
                ->sum(DB::raw('children_qty + students_qty + adults_qty'));

            $totalToBook = $request->children_qty + $request->students_qty + $request->adults_qty;

            // ตรวจสอบความจุเฉพาะเมื่อมีกิจกรรมที่มี max_capacity
            if ($activity->max_capacity !== null) {
                if ($totalBooked + $totalToBook > $activity->max_capacity) {
                    return back()->with('error', 'รอบการเข้าชมนี้เต็มแล้วหรือเกินความจุสูงสุด');
                }
            }

            // ตรวจสอบว่ามีกิจกรรม activity_id = 3 ที่ถูกจองไว้ในช่วงเวลาที่ใกล้เคียงกัน
            $conflictingBookingForActivity3 = Bookings::where('booking_date', $request->booking_date)
                ->where('activity_id', 3)
                ->whereHas('timeslot', function ($query) use ($timeslot) {
                    $query->where(function ($q) use ($timeslot) {
                        $q->whereTime('start_time', '<', $timeslot->end_time)
                            ->whereTime('end_time', '>', $timeslot->start_time);
                    });
                })
                ->where('status', 1) // สถานะอนุมัติเท่านั้น
                ->exists();

            // หากมีกิจกรรม activity_id = 3 ที่ถูกจองไว้ในช่วงเวลาที่ใกล้เคียงกัน จะไม่อนุญาตให้จองกิจกรรมใหม่
            if ($conflictingBookingForActivity3 && $request->input('fk_activity_id') != 3) {
                return back()->with('error', 'ไม่สามารถจองกิจกรรมนี้ได้เนื่องจากมีกิจกรรมที่จองไว้ก่อนหน้านี้ในช่วงเวลาที่ใกล้เคียงกัน');
            }
        } else {
            // หากไม่มี timeslot ให้ข้ามการตรวจสอบความจุ
            $totalToBook = $request->children_qty + $request->students_qty + $request->adults_qty;

            // เช็คว่า activity มี max_capacity หรือไม่
            if ($activity->max_capacity !== null) {
                // ถ้า max_capacity มีค่า (กรณีที่มีกิจกรรมที่มีความจุ) ให้ตรวจสอบ
                $totalBooked = Bookings::where('booking_date', $request->booking_date)
                    ->whereNull('timeslots_id') // Consider bookings without timeslots
                    ->where('status', 1) // สถานะอนุมัติเท่านั้น
                    ->sum(DB::raw('children_qty + students_qty + adults_qty'));

                if ($totalBooked + $totalToBook > $activity->max_capacity) {
                    return back()->with('error', 'จำนวนการจองในวันนี้เต็มแล้วหรือเกินความจุสูงสุด');
                }
            }
        }

        // Create or find the institute
        $institute = Institutes::firstOrCreate(
            [
                'instituteName' => $request->instituteName,
                'instituteAddress' => $request->instituteAddress,
                'province' => $request->province,
                'district' => $request->district,
                'subdistrict' => $request->subdistrict,
                'zipcode' => $request->zipcode,
            ]
        );

        $visitor = Visitors::firstOrCreate(
            [
                'visitorName' => $request->visitorName,
                'visitorEmail' => $request->visitorEmail,
                'tel' => $request->tel,
            ]
        );
        $visitor->institute_id = $institute->institute_id;
        $visitor->save();

        // บันทึกการจองใหม่
        $booking = new Bookings();
        $booking->activity_id = $request->input('fk_activity_id');
        $booking->timeslots_id = $request->input('fk_timeslots_id') ?? null;
        $booking->institute_id = $institute->institute_id;
        $booking->visitor_id = $visitor->visitor_id;
        $booking->booking_date = $request->booking_date;
        $booking->children_qty = $request->children_qty ?? 0;
        $booking->students_qty = $request->students_qty ?? 0;
        $booking->adults_qty = $request->adults_qty ?? 0;
        $booking->status = false;

        $booking->save();

        // return redirect()->back()->with('success', 'จองเข้าชมพิพิธภัณฑ์เรียบร้อยแล้ว');
        return redirect()->route('showBookingStatus', ['bookingId' => $booking->booking_id]);
    }

    public function showBookingStatus(Request $request)
    {
        $bookingId = $request->query('bookingId');
        $visitorEmail = $request->query('visitorEmail');

        if ($bookingId) {
            // ค้นหาข้อมูลการจองโดยใช้ bookingId
            $booking = Bookings::find($bookingId);
        } elseif ($visitorEmail) {
            // ค้นหาข้อมูลการจองโดยใช้ email ของผู้จอง
            $booking = Bookings::where('visitorEmail', $visitorEmail)->latest()->first();
        } else {
            // หากไม่ได้ส่งข้อมูลมาทั้ง bookingId หรือ visitorEmail
            $booking = null;
        }

        if ($booking) {
            return view('showBookingStatus', compact('booking'));
        } else {
            return redirect()->route('home')->with('error', 'ไม่พบข้อมูลการจอง');
        }
    }


    public function showBookingForm($activity_id)
    {
        // ดึงราคาของกิจกรรมจากฐานข้อมูล
        $activity = DB::table('activities')->where('activity_id', $activity_id)->first();

        // ส่งข้อมูลราคากิจกรรมไปยัง view
        return view('form_bookings', [
            'activity' => $activity
        ]);
    }

    public function showGeneralBookingForm()
    {
        $activities = Activity::where('activity_type_id', 1)->get(); // ดึงกิจกรรมที่มี activity_type_id = 1
        return view('form_bookings', ['activities' => $activities]);
    }

    public function searchBookingByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $bookings = Bookings::whereHas('visitor', function ($query) use ($email) {
            $query->where('visitorEmail', $email);
        })->get();

        if ($bookings->isEmpty()) {
            return redirect()->route('checkBookingStatus')
                ->with('error', 'ไม่พบข้อมูลการจองในระบบสำหรับอีเมลนี้');
        }

        return view('checkBookingStatus', compact('bookings', 'email'));
    }

    public function checkBookingStatus()
    {
        return view('checkBookingStatus');
    }

}
