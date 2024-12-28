<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bookings;
use App\Models\Timeslots;
use App\Models\Activity;
use App\Models\Institutes;
use App\Models\Visitors;
use App\Models\StatusChanges;
use App\Models\closedTimeslots;
use DateTime;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingApprovedMail;
use Illuminate\Support\Facades\Log;

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

            if ($item->timeslot) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('timeslots_id', $item->timeslot->timeslots_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty +  monk_qty'));
            } else {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty +  monk_qty'));
            }
            $maxCapacity = $item->activity->max_capacity;

            if ($maxCapacity === null) {
                $item->remaining_capacity = 'ไม่จำกัดจำนวนคน';
            } else {
                $item->remaining_capacity = $maxCapacity - $totalApproved;
            }
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice + $disabledPrice + $elderlyPrice + $monkPrice;
        }
        return view('admin.generalRequest.request_bookings', compact('requestBookings'));
    }

    function showApproved(Request $request)
    {
        $activities = Activity::where('activity_type_id', 1)->get();

        $query = Bookings::with('activity', 'timeslot', 'visitor', 'institute')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
            ->where('status', 1);

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        $approvedBookings = $query->paginate(5);


        foreach ($approvedBookings as $item) {
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('activity_id', $item->activity_id)
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty +  monk_qty'));

            if ($item->timeslot && $item->timeslot->timeslots_id) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('timeslots_id', $item->timeslot->timeslots_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty +  monk_qty'));
            }

            if ($item->activity->max_capacity !== null) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
            } else {
                $item->remaining_capacity = 'ไม่จำกัดจำนวนคน';
            }

            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;
        }

        return view('admin.generalRequest.approved_bookings', compact('approvedBookings', 'activities'));
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
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty +  monk_qty'));
            }
            if ($item->activity) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
                $item->remaining_capacity += $item->children_qty + $item->students_qty + $item->adults_qty + $item->disabled_qty + $item->elderly_qty + $item->monk_qty;
            } else {
                $item->remaining_capacity = 'N/A';
            }
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;
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

        $statusChange = StatusChanges::where('booking_id', $booking_id)->first();

        if ($statusChange) {
            $statusChange->old_status = $oldStatus;
            $statusChange->new_status = $newStatus;
            $statusChange->comments = $request->input('comments', $statusChange->comments);
            $statusChange->changed_by = Auth::user()->name;
            $statusChange->save();
        } else {
            StatusChanges::create([
                'booking_id' => $booking->booking_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'comments' => $request->input('comments', null),
                'changed_by' => Auth::user()->name,
            ]);
        }
        // ดึงอีเมลจากตาราง visitors ผ่านความสัมพันธ์
        $visitorEmail = $booking->visitor ? $booking->visitor->visitorEmail : null;

        if ($newStatus === 1 && $visitorEmail) {
            // ส่งอีเมลหากพบอีเมล
            Mail::to($visitorEmail)->send(new BookingApprovedMail($booking));
        } else {
            Log::warning("ไม่พบอีเมลสำหรับการจองหมายเลข {$booking->booking_id}");
        }

        return redirect()->back()->with('success', 'สถานะการจองถูกอัปเดตแล้ว');
    }
    function InsertBooking(Request $request)
    {
        $request->validate(
            [
                'fk_activity_id' => 'required|exists:activities,activity_id',
                'fk_timeslots_id' => 'nullable|exists:timeslots,timeslots_id',
                'booking_date' => 'required|date_format:d/m/Y',
                'instituteName' => 'required',
                'instituteAddress' => 'required',
                'province' => 'required',
                'district' => 'required',
                'subdistrict' => 'required',
                'zipcode' => ['required', 'regex:/^[0-9]{5}$/'],
                'visitorName' => 'required',
                'visitorEmail' => 'required|email',
                'tel' => ['required', 'regex:/^[0-9]{10}$/'],
                'children_qty' => 'nullable|integer|min:0',
                'students_qty' => 'nullable|integer|min:0',
                'adults_qty' => 'nullable|integer|min:0',
                'disabled_qty' => 'nullable|integer|min:0',
                'elderly_qty' => 'nullable|integer|min:0',
                'monk_qty' => 'nullable|integer|min:0',
            ]
        );
        if (in_array($request->fk_activity_id, [1, 2, 3])) {
            $rules['fk_timeslots_id'] = 'required|exists:timeslots,timeslots_id';
        } else {
            $rules['fk_timeslots_id'] = 'nullable|exists:timeslots,timeslots_id';
        }
        $messages = [
            'fk_timeslots_id.required' => 'กรุณาเลือกรอบการเข้าชม',
            'at_least_one_quantity.required' => 'กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท',
        ];

        $request->validate($rules, $messages);

        $quantityFields = [
            'children_qty',
            'students_qty',
            'adults_qty',
            'disabled_qty',
            'elderly_qty',
            'monk_qty'
        ];

        $isAtLeastOneQuantityFilled = false;
        foreach ($quantityFields as $field) {
            if ($request->$field > 0) {
                $isAtLeastOneQuantityFilled = true;
                break;
            }
        }

        if (!$isAtLeastOneQuantityFilled) {
            return back()->withErrors([
                'at_least_one_quantity' => 'กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท'
            ])->withInput();
        }

        $activity = Activity::with('activityType')->find($request->fk_activity_id);
        if (!$activity) {
            return back()->with('error', 'ไม่พบกิจกรรม')->withInput();
        }

        $bookingDate = DateTime::createFromFormat('d/m/Y', $request->booking_date);
        if (!$bookingDate) {
            return back()->with('error', 'รูปแบบวันที่ไม่ถูกต้อง')->withInput();
        }
        $formattedDate = $bookingDate->format('Y-m-d');

        $isAllClosed = ClosedTimeslots::whereNull('timeslots_id')
            ->where('activity_id', $activity->activity_id)
            ->where('closed_on', $formattedDate)
            ->exists();

        if ($isAllClosed) {
            return back()->with('error', 'ไม่สามารถจองได้เนื่องจากรอบการเข้าชมถูกปิด')->withInput();
        }

        if ($request->filled('fk_timeslots_id')) {
            $timeslot = Timeslots::find($request->fk_timeslots_id);
            if (!$timeslot) {
                return back()->with('error', 'ไม่พบรอบการเข้าชม')->withInput();
            }

            $isClosed = ClosedTimeslots::where('timeslots_id', $timeslot->timeslots_id)
                ->where('closed_on', $formattedDate)
                ->exists();

            if ($isClosed) {
                return back()->with('error', 'รอบการเข้าชมนี้ถูกปิด')->withInput();
            }

            if ($activity->activity_id == 3) {
                if ($request->filled('fk_timeslots_id')) {
                    $timeslot = Timeslots::find($request->fk_timeslots_id);
                    if (!$timeslot) {
                        return back()->with('error', 'ไม่พบรอบการเข้าชม')->withInput();
                    }
                    $conflictingBooking = Bookings::join('timeslots', 'bookings.timeslots_id', '=', 'timeslots.timeslots_id')
                        ->whereIn('bookings.activity_id', [1, 2])
                        ->where('bookings.booking_date', $formattedDate)
                        ->where(function ($query) use ($timeslot) {
                            $query->whereBetween('timeslots.start_time', [$timeslot->start_time, $timeslot->end_time])
                                ->orWhereBetween('timeslots.end_time', [$timeslot->start_time, $timeslot->end_time]);
                        })
                        ->exists();

                    if ($conflictingBooking) {
                        return back()->with('error', 'ไม่สามารถจองกิจกรรมนี้ได้ เนื่องจากมีกิจกรรมที่จองในช่วงเวลาใกล้เคียงกันจากกิจกรรมอื่น')->withInput();
                    }
                }
            }
            $totalToBook = ($request->children_qty ?? 0) + ($request->students_qty ?? 0) + ($request->adults_qty ?? 0);
            $totalBooked = Bookings::where('booking_date', $formattedDate)
                ->where('timeslots_id', $timeslot->timeslots_id)
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + disabled_qty + elderly_qty + monk_qty'));
            if ($activity->max_capacity !== null && $totalBooked + $totalToBook > $activity->max_capacity) {
                return back()->with('error', 'จำนวนเกินความจุต่อรอบการเข้าชม')->withInput();
            }
        }

        $institute = Institutes::firstOrCreate([
            'instituteName' => $request->instituteName,
            'instituteAddress' => $request->instituteAddress,
            'province' => $request->province,
            'district' => $request->district,
            'subdistrict' => $request->subdistrict,
            'zipcode' => $request->zipcode,
        ]);
        $visitor = Visitors::updateOrCreate(
            [
                'visitorEmail' => $request->visitorEmail,
            ],
            [
                'visitorName' => $request->visitorName,
                'tel' => $request->tel,
                'institute_id' => $institute->institute_id,
            ]
        );

        $booking = new Bookings();
        $booking->activity_id = $request->fk_activity_id;
        $booking->timeslots_id = $request->fk_timeslots_id ?? null;
        $booking->institute_id = $institute->institute_id;
        $booking->visitor_id = $visitor->visitor_id;
        $booking->booking_date = $formattedDate;
        $booking->children_qty = $request->children_qty ?? 0;
        $booking->students_qty = $request->students_qty ?? 0;
        $booking->adults_qty = $request->adults_qty ?? 0;
        $booking->disabled_qty = $request->disabled_qty ?? 0;
        $booking->elderly_qty = $request->elderly_qty ?? 0;
        $booking->monk_qty = $request->monk_qty ?? 0;
        $booking->status = false;
        $booking->save();

        return back()->with('showSuccessModal', true);
    }

    public function showBookingForm($activity_id)
    {
        $selectedActivity = Activity::find($activity_id);
        if (!$selectedActivity) {
            return redirect()->back()->with('error', 'Activity not found.');
        }

        $timeslots = Timeslots::where('activity_id', $activity_id)->get();

        return view('form_bookings', [
            'activity_id' => $activity_id,
            'selectedActivity' => $selectedActivity,
            'timeslots' => $timeslots,
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
