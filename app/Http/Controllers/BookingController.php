<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bookings;
use App\Models\Timeslots;
use App\Models\Activity;
use App\Models\StatusChanges;

use Carbon\Carbon;

class BookingController extends Controller
{
    function showBookings()
    {
        $requestBookings = Bookings::with(['activity', 'timeslot'])
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
            ->where('status', 0)
            ->paginate(5);

        foreach ($requestBookings as $item) {
            // Calculate total approved bookings for the same booking_date and timeslot
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('timeslots_id', $item->timeslot->timeslots_id)
                ->where('status', 1) // Only count approved bookings
                ->sum(DB::raw('children_qty + students_qty + adults_qty'));

            // For unapproved bookings, keep max_capacity without adjustments
            $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;

            // Calculate total price
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;

            // Calculate total price
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;

            // Calculate total visitors
            $item->totalVisitors = $item->children_qty + $item->students_qty + $item->adults_qty;
        }

        return view('admin.generalRequest.request_bookings', compact('requestBookings'));
    }

    function showApproved()
    {
        $approvedBookings = Bookings::with('activity', 'timeslot')
            ->where('status', 1) // Only fetch approved bookings
            ->paginate(5);

        foreach ($approvedBookings as $item) {
            // Calculate total approved bookings for the same booking_date and timeslot
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('timeslots_id', $item->timeslot->timeslots_id)
                ->where('status', 1) // Only count approved bookings
                ->sum(DB::raw('children_qty + students_qty + adults_qty'));

            // For approved bookings, adjust the remaining capacity
            $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;

            // Calculate total price
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;

            // Calculate total price
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;

            // Calculate total visitors
            $item->totalVisitors = $item->children_qty + $item->students_qty + $item->adults_qty;
        }

        return view('admin.generalRequest.approved_bookings', compact('approvedBookings'));
    }

    function showExcept()
    {
        $exceptBookings = Bookings::with('activity', 'timeslot')
            ->where('status', 2) // Only fetch canceled bookings (status = 2)
            ->paginate(5);

        foreach ($exceptBookings as $item) {
            // Calculate total approved bookings for the same booking_date and timeslot
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('timeslots_id', $item->timeslot->timeslots_id)
                ->where('status', 1) // Only count approved bookings
                ->sum(DB::raw('children_qty + students_qty + adults_qty'));

            // Set remaining capacity initially, subtract approved bookings
            $item->remaining_capacity = $item->actvity->max_capacity - $totalApproved;

            // Since the status is 2 (canceled), add back the visitors of this booking to remaining capacity
            $item->remaining_capacity += $item->children_qty + $item->students_qty + $item->adults_qty;

            // Calculate total price (for reference or auditing purposes)
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;

            // Calculate total price
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;

            // Calculate total visitors
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

        // Store the old status before changing
        $oldStatus = $booking->status;

        // Map status values to database values
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

        // Update booking status
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
                // 'fk_timeslots_id' => 'required|exists:timeslots,timeslots_id',
                'fk_timeslots_id' => [
                    'nullable',
                    'exists:timeslots,timeslots_id',
                    function ($attribute, $value, $fail) use ($request) {
                        // หาก activity_id ไม่ใช่กิจกรรมที่ไม่ต้องมี timeslot (เช่น activity_id 1 และ 2 ต้องมีรอบการเข้าชม)
                        if (in_array($request->input('fk_activity_id'), [1, 2]) && !$value) {
                            $fail('กรุณาเลือกรอบการเข้าชม');
                        }
                    }
                ],
                'booking_date' => 'required',
                'instituteName' => 'required',
                'instituteAddress' => 'required',
                'province' => 'required',
                'district' => 'required',
                'subdistrict' => 'required',
                'zip' => [
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


            ],
            [
                'booking_date.required' => 'กรุณาเลือกวันที่ต้องการจอง',
                'instituteName.required' => 'กรุณาป้อนชื่อหน่วยงาน',
                'instituteAddress.required' => 'กรุณาป้อนที่อยู่หน่วยงาน',
                'province.required' => 'กรุณาเลือกจังหวัด',
                'district.required' => 'กรุณาเลือกเขต/อำเภอ',
                'subdistrict.required' => 'กรุณาเลือกแขวง/ตำบล',
                'zip.required' => 'กรุณาป้อนรหัสไปรษณีย์',
                'zip.max' => 'กรุณาป้อนรหัสไปรษณีย์จำนวน 5 หลัก',
                'zip.regex' => 'รหัสไปรษณีย์ต้องเป็นตัวเลขจำนวน 5 หลัก',
                'visitorName.required' => 'กรุณาป้อน ชื่อ-นามสกุล',
                'visitorEmail.required' => 'กรุณาป้อนอีเมล',
                'visitorEmail.email' => 'กรุณาป้อนอีเมลที่ถูกต้อง',
                'tel.required' => 'กรุณาป้อนหมายเลขโทรศัพท์',
                'tel.max' => 'กรุณากรอกตัวเลข 10 ตัว',
                'tel.regex' => 'เบอร์โทรต้องเป็นตัวเลขจำนวน 10 หลัก',
            ]
        );
        // Fetch the selected timeslot
        $timeslot = Timeslots::find($request->input('fk_timeslots_id'));
        // Fetch the selected activity to get max_capacity
        $activity = Activity::find($request->input('fk_activity_id'));

        // Calculate total booked for the selected date and timeslot
        $totalBooked = Bookings::where('booking_date', $request->booking_date)
            ->where('timeslots_id', $timeslot->timeslots_id)
            ->sum(DB::raw('children_qty + students_qty + adults_qty'));

        // Calculate the total to book
        $totalToBook = $request->children_qty + $request->students_qty + $request->adults_qty;

        // Check if total booked plus new booking exceeds max_capacity
        if ($totalBooked + $totalToBook > $activity->max_capacity) {
            return back()->with('error', 'รอบการเข้าชมนี้เต็มแล้วหรือเกินความจุสูงสุด');
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
            ->where('status', '!=', 2) // ไม่สนใจการจองที่ถูกยกเลิก
            ->exists();

        // หากมีกิจกรรม activity_id = 3 ที่ถูกจองไว้ในช่วงเวลาที่ใกล้เคียงกัน จะไม่อนุญาตให้จองกิจกรรมใหม่
        if ($conflictingBookingForActivity3 && $request->input('fk_activity_id') != 3) {
            return back()->with('error', 'ไม่สามารถจองกิจกรรมนี้ได้เนื่องจากมีกิจกรรมที่จองไว้ก่อนหน้านี้ในช่วงเวลาที่ใกล้เคียงกัน');
        }

        // หากไม่เกินความจุสูงสุด ให้ทำการบันทึกข้อมูลการจอง
        $booking = new Bookings();
        $booking->activity_id = $request->input('fk_activity_id');
        $booking->timeslots_id = $request->input('fk_timeslots_id');
        $booking->booking_date = $request->booking_date;
        $booking->instituteName = $request->instituteName;
        $booking->instituteAddress = $request->instituteAddress;
        $booking->province = $request->province;
        $booking->district = $request->district;
        $booking->subdistrict = $request->subdistrict;
        $booking->zip = $request->zip;
        $booking->visitorName = $request->visitorName;
        $booking->visitorEmail = $request->visitorEmail;
        $booking->tel = $request->tel;
        $booking->children_qty = $request->children_qty ?? 0;
        $booking->students_qty = $request->students_qty ?? 0;
        $booking->adults_qty = $request->adults_qty ?? 0;
        $booking->status = false;

        $booking->save(); // บันทึกข้อมูลการจอง

        return redirect()->back()->with('success', 'จองเข้าชมพิพิธภัณฑ์เรียบร้อยแล้ว');

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

}
