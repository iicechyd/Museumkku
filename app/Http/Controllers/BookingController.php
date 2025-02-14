<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DateTime;
use App\Models\Bookings;
use App\Models\Timeslots;
use App\Models\Activity;
use App\Models\SubActivity;
use App\Models\Institutes;
use App\Models\Visitors;
use App\Models\StatusChanges;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingApprovedMail;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingPendingMail;

class BookingController extends Controller
{
    function showTodayBookings(Request $request)
    {
        $today = Carbon::today();

        $activities = Activity::where('activity_type_id', 1)
            ->get()
            ->map(function ($activity) use ($today) {
                $countBookings = Bookings::where('activity_id', $activity->activity_id)
                    ->whereDate('booking_date', $today)
                    ->where('status', 1)
                    ->count();

                $activity->countBookings = $countBookings;
                return $activity;
            });

        $query = Bookings::with('activity', 'timeslot', 'visitor', 'institute', 'documents', 'subactivities')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
            ->where('status', 1)
            ->whereDate('booking_date', $today);

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        $approvedBookings = $query->paginate(5);

        foreach ($approvedBookings as $item) {
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('activity_id', $item->activity_id)
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));

            if ($item->timeslot && $item->timeslot->timeslots_id) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('timeslots_id', $item->timeslot->timeslots_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));
            }

            if ($item->activity->max_capacity !== null) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
            } else {
                $item->remaining_capacity = 'ไม่จำกัดจำนวนคน';
            }

            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $kidPrice = $item->kid_qty * $item->activity->kid_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;
        }

        return view('admin.generalRequest.manage_bookings', compact('approvedBookings', 'activities'));
    }

    function showBookings(Request $request)
    {
        $activities = Activity::where('activity_type_id', 1)
            ->get()
            ->map(function ($activity) {
                $countBookings = Bookings::where('activity_id', $activity->activity_id)
                    ->where('status', 0)
                    ->count();

                $activity->countBookings = $countBookings;
                return $activity;
            });

        $query = Bookings::with('activity', 'timeslot', 'visitor', 'institute', 'subactivities')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
            ->where('status', 0);

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        $requestBookings = $query->paginate(5);

        foreach ($requestBookings as $item) {
            $totalApproved = 0;

            if ($item->timeslot) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('timeslots_id', $item->timeslot->timeslots_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));
            } else {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));
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
            $kidPrice = $item->kid_qty * $item->activity->kid_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice + $disabledPrice + $elderlyPrice + $monkPrice;
        }
        return view('admin.generalRequest.request_bookings', compact('requestBookings', 'activities'));
    }

    function showApproved(Request $request)
    {
        $activities = Activity::where('activity_type_id', 1)
            ->get()
            ->map(function ($activity) {
                $countBookings = Bookings::where('activity_id', $activity->activity_id)
                    ->where('status', 1)
                    ->count();

                $activity->countBookings = $countBookings;
                return $activity;
            });

        $query = Bookings::with('activity', 'timeslot', 'visitor', 'institute', 'documents', 'subactivities')
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
                ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));

            if ($item->timeslot && $item->timeslot->timeslots_id) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('timeslots_id', $item->timeslot->timeslots_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));
            }

            if ($item->activity->max_capacity !== null) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
            } else {
                $item->remaining_capacity = 'ไม่จำกัดจำนวนคน';
            }

            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $kidPrice = $item->kid_qty * $item->activity->kid_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;
            $item->signed_edit_url = URL::signedRoute('admin.edit_booking', ['booking_id' => $item->booking_id]);
        }

        return view('admin.generalRequest.approved_bookings', compact('approvedBookings', 'activities'));
    }

    function showExcept(Request $request)
    {
        $activities = Activity::where('activity_type_id', 1)
            ->get()
            ->map(function ($activity) {
                $countBookings = Bookings::where('activity_id', $activity->activity_id)
                    ->where('status', 3)
                    ->count();

                $activity->countBookings = $countBookings;
                return $activity;
            });

        $query = Bookings::with('activity', 'timeslot', 'visitor', 'institute', 'subactivities')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
            ->where('status', 3);

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        $exceptBookings = $query->paginate(5);

        foreach ($exceptBookings as $item) {
            $totalApproved = 0;
            if ($item->timeslot) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('timeslots_id', $item->timeslot->timeslots_id)
                    ->where('status', 1)
                    ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));
            }
            if ($item->activity) {
                $item->remaining_capacity = $item->activity->max_capacity - $totalApproved;
                $item->remaining_capacity += $item->children_qty + $item->students_qty + $item->adults_qty + $item->kid_qty + $item->disabled_qty + $item->elderly_qty + $item->monk_qty;
            } else {
                $item->remaining_capacity = 'N/A';
            }
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $kidPrice = $item->kid_qty * $item->activity->kid_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice;
        }
        return view('admin.generalRequest.except_cases_bookings', compact('exceptBookings', 'activities'));
    }

    public function updateStatus(Request $request, $booking_id)
    {

        $request->validate([
            'status' => 'required|in:pending,approve,checkin,cancel',
            'comments' => 'nullable|string',
            'actual_children_qty' => 'integer|min:0',
            'actual_students_qty' => 'integer|min:0',
            'actual_adults_qty' => 'integer|min:0',
            'actual_kid_qty' => 'integer|min:0',
            'actual_disabled_qty' => 'integer|min:0',
            'actual_elderly_qty' => 'integer|min:0',
            'actual_monk_qty' => 'integer|min:0',
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
            case 'checkin':
                $newStatus = 2;
                break;
            case 'cancel':
                $newStatus = 3;
                break;
        }

        $booking->status = $newStatus;
        $booking->save();

        $statusChange = StatusChanges::where('booking_id', $booking_id)->first();

        if ($statusChange) {
            $statusChange->old_status = $oldStatus;
            $statusChange->new_status = $newStatus;
            $statusChange->comments = $request->input('comments', $statusChange->comments);
            $statusChange->actual_children_qty = $request->input('actual_children_qty', 0);
            $statusChange->actual_students_qty = $request->input('actual_students_qty', 0);
            $statusChange->actual_adults_qty = $request->input('actual_adults_qty', 0);
            $statusChange->actual_kid_qty = $request->input('actual_kid_qty', 0);
            $statusChange->actual_disabled_qty = $request->input('actual_disabled_qty', 0);
            $statusChange->actual_elderly_qty = $request->input('actual_elderly_qty', 0);
            $statusChange->actual_monk_qty = $request->input('actual_monk_qty', 0);
            $statusChange->changed_by = Auth::user()->name;
            $statusChange->save();
        } else {
            $statusChange = StatusChanges::create([
                'booking_id' => $booking->booking_id,
                'user_id' => Auth::user()->user_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'comments' => $request->input('comments', null),
                'actual_children_qty' => $request->input('actual_children_qty', 0),
                'actual_students_qty' => $request->input('actual_students_qty', 0),
                'actual_adults_qty' => $request->input('actual_adults_qty', 0),
                'actual_kid_qty' => $request->input('actual_kid_qty', 0),
                'actual_disabled_qty' => $request->input('actual_disabled_qty', 0),
                'actual_elderly_qty' => $request->input('actual_elderly_qty', 0),
                'actual_monk_qty' => $request->input('actual_monk_qty', 0),
                'changed_by' => Auth::user()->name,
            ]);
        }

        $visitorEmail = $booking->visitor ? $booking->visitor->visitorEmail : null;

        if ($newStatus === 1 && $visitorEmail) {
            $uploadLink = route('documents.upload', ['booking_id' => $booking->booking_id]);
            $cancelLink = route('bookings.cancel', ['booking_id' => $booking->booking_id]);
            Mail::to($visitorEmail)->send(new BookingApprovedMail($booking, $uploadLink));
        } elseif ($newStatus === 3 && $visitorEmail) {
            Mail::to($visitorEmail)->send(new BookingCancelledMail($booking));
        } else {
            Log::warning("ไม่พบอีเมลสำหรับการจองหมายเลข {$booking->booking_id}");
        }
        return redirect()->back()->with('success', 'สถานะการจองถูกอัปเดตแล้ว');
    }
    function InsertBooking(Request $request)
    {
        $rules = [
            'fk_activity_id' => 'required|exists:activities,activity_id',
            'fk_timeslots_id' => 'nullable|exists:timeslots,timeslots_id',
            'sub_activity_id' => 'nullable|array',
            'sub_activity_id.*' => 'nullable|exists:sub_activities,sub_activity_id',
            'booking_date' => 'required|date_format:d/m/Y',
            'instituteName' => 'required',
            'instituteAddress' => 'required',
            'province' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'subdistrict' => 'required|string|max:100',
            'zipcode' => ['required', 'regex:/^[0-9]{5}$/'],
            'visitorName' => 'required',
            'visitorEmail' => 'required|email',
            'tel' => ['required', 'regex:/^[0-9]{10}$/', 'starts_with:0'],
            'children_qty' => 'nullable|integer|min:0',
            'students_qty' => 'nullable|integer|min:0',
            'adults_qty' => 'nullable|integer|min:0',
            'kid_qty' => 'nullable|integer|min:0',
            'disabled_qty' => 'nullable|integer|min:0',
            'elderly_qty' => 'nullable|integer|min:0',
            'monk_qty' => 'nullable|integer|min:0',
            'note' => 'nullable|string'
        ];

        if (in_array($request->fk_activity_id, [1, 2, 3])) {
            $rules['fk_timeslots_id'] = 'required|exists:timeslots,timeslots_id';
        }
        $messages = [
            'fk_timeslots_id.required' => 'กรุณาเลือกรอบการเข้าชม',
            'booking_date.required' => 'กรุณาระบุวันที่จองเข้าชม',
            'instituteName.required' => 'กรุณากรอกชื่อหน่วยงาน',
            'instituteAddress.required' => 'กรุณากรอกที่อยู่หน่วยงาน',
            'province.required' => 'กรุณากรอกจังหวัด',
            'district.required' => 'กรุณากรอกเขต/อำเภอ',
            'subdistrict.required' => 'กรุณากรอกแขวน/ตำบล',
            'zipcode.required' => 'กรุณกรอกรหัสไปรษณีย์',
            'visitorName.required' => 'กรุณกรอกชื่อผู้ประสานงาน',
            'visitorEmail.required' => 'กรุณกรอกอีเมล์ผู้ประสานงาน',
            'tel.required' => 'กรุณกรอกเบอร์โทรผู้ประสานงาน',
            'tel.regex' => 'กรุณากรอกเบอร์โทรในรูปแบบที่ถูกต้อง (10 หลัก)',
            'tel.starts_with' => 'เบอร์โทรต้องขึ้นต้นด้วย 0',
            'at_least_one_quantity.required' => 'กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        $activity = Activity::find($request->fk_activity_id);
        if (!$activity) {
            return back()->with('error', 'ไม่พบกิจกรรม')->withInput();
        }
        $maxSubactivities = $activity->max_subactivities;
        $selectedSubactivities = $request->input('sub_activity_id', []);
        if (count($selectedSubactivities) > $maxSubactivities) {
            return back()->withErrors([
                'sub_activity_id' => "คุณสามารถเลือกได้สูงสุด $maxSubactivities กิจกรรมย่อยเท่านั้น"
            ])->withInput();
        }
        $quantityFields = [
            'children_qty',
            'students_qty',
            'adults_qty',
            'kid_qty',
            'disabled_qty',
            'elderly_qty',
            'monk_qty'
        ];

        $isAtLeastOneQuantityFilled = false;
        $totalToBook = 0;

        foreach ($quantityFields as $field) {
            if ($request->$field > 0) {
                $isAtLeastOneQuantityFilled = true;
            }
            $totalToBook += $request->$field ?? 0;
        }

        if (!$isAtLeastOneQuantityFilled) {
            return back()->withErrors([
                'at_least_one_quantity' => 'กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท'
            ])->withInput();
        }
        if ($totalToBook < 50) {
            session()->flash('error', 'กรุณาจองขั้นต่ำ 50 คน');
            return back()->withInput();
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

        if ($request->filled('fk_timeslots_id')) {
            $timeslot = Timeslots::find($request->fk_timeslots_id);
            if (!$timeslot) {
                return back()->with('error', 'ไม่พบรอบการเข้าชม')->withInput();
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
                            $query->where('timeslots.start_time', '<', $timeslot->end_time)
                                ->where('timeslots.end_time', '>', $timeslot->start_time);
                        })
                        ->exists();

                    if ($conflictingBooking) {
                        return back()->with('error', 'ไม่สามารถจองกิจกรรมนี้ได้ เนื่องจากมีกิจกรรมที่จองในช่วงเวลาใกล้เคียงกันจากกิจกรรมอื่น กรุณาจองช่วงเวลาอื่น')->withInput();
                    }
                }
            }
            if (in_array($activity->activity_id, [1, 2])) {
                if ($request->filled('fk_timeslots_id')) {
                    $timeslot = Timeslots::find($request->fk_timeslots_id);
                    if (!$timeslot) {
                        return back()->with('error', 'ไม่พบรอบการเข้าชม')->withInput();
                    }
                    $conflictingBooking = Bookings::join('timeslots', 'bookings.timeslots_id', '=', 'timeslots.timeslots_id')
                        ->where('bookings.activity_id', 3)
                        ->where('bookings.booking_date', $formattedDate)
                        ->where(function ($query) use ($timeslot) {
                            $query->where('timeslots.start_time', '<', $timeslot->end_time)
                                ->where('timeslots.end_time', '>', $timeslot->start_time);
                        })
                        ->exists();

                    if ($conflictingBooking) {
                        return back()->with('error', 'ไม่สามารถจองกิจกรรมนี้ได้ เนื่องจากมีการจองกิจกรรมอื่นในช่วงเวลาใกล้เคียง กรุณาจองช่วงเวลาอื่น')->withInput();
                    }
                }
            }
            $totalToBook = ($request->children_qty ?? 0)
                + ($request->students_qty ?? 0)
                + ($request->adults_qty ?? 0)
                + ($request->kid_qty ?? 0)
                + ($request->disabled_qty ?? 0)
                + ($request->elderly_qty ?? 0)
                + ($request->monk_qty ?? 0);
            $totalBooked = Bookings::where('booking_date', $formattedDate)
                ->where('timeslots_id', $timeslot->timeslots_id)
                ->whereIn('status', [0, 1])
                ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty + monk_qty'));
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
        $booking->kid_qty = $request->kid_qty ?? 0;
        $booking->disabled_qty = $request->disabled_qty ?? 0;
        $booking->elderly_qty = $request->elderly_qty ?? 0;
        $booking->monk_qty = $request->monk_qty ?? 0;
        $booking->note = $request->note ?? null;
        $booking->status = false;
        $booking->save();

        if ($request->has('sub_activity_id')) {
            $subActivities = $request->input('sub_activity_id');
            $booking->subActivities()->sync($subActivities);
        }

        $editLink = route('bookings.edit', ['booking_id' => $booking->booking_id]);
        $cancelLink = route('bookings.cancel', ['booking_id' => $booking->booking_id]);
        Mail::to($request->visitorEmail)->send(new BookingPendingMail($booking));
        return back()->with('showSuccessModal', true);
    }

    public function showBookingForm($activity_id)
    {
        if (!session()->has('verification_email')) {
            session(['redirect_url' => route('form_bookings.activity', ['activity_id' => $activity_id])]);
            return redirect()->route('guest.verify');
        }

        $selectedActivity = Activity::find($activity_id);
        if (!$selectedActivity) {
            return redirect()->back()->with('error', 'Activity not found.');
        }

        $timeslots = Timeslots::where('activity_id', $activity_id)->get();
        $subactivities = SubActivity::where('activity_id', $activity_id)
            ->where('status', 1)
            ->get();
        $hasSubactivities = $subactivities->isNotEmpty();

        return view('form_bookings', [
            'activity_id' => $activity_id,
            'selectedActivity' => $selectedActivity,
            'timeslots' => $timeslots,
            'subactivities' => $subactivities,
            'hasSubactivities' => $hasSubactivities,
            'maxSubactivities' => $selectedActivity->max_subactivities,
        ]);
    }

    public function searchBookingByEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');

        $bookings = Bookings::whereIn('status', [0, 1])
            ->whereHas('visitor', function ($query) use ($email) {
                $query->where('visitorEmail', $email);
            })
            ->get();

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

    public function showHistory(Request $request)
    {
        $query = Bookings::with([
            'institute',
            'activity',
            'documents',
            'timeslot',
            'statusChanges'
        ])->whereIn('status', [2, 3])->orderBy('created_at', 'desc');

        if ($request->filled('activity_name')) {
            $query->whereHas('activity', function ($q) use ($request) {
                $q->where('activity_name', $request->activity_name);
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('statusChanges', function ($q) use ($request) {
                $q->where('new_status', $request->status);
            });
        }
        $histories = $query->get();
        $activities = Activity::orderBy('activity_name')->pluck('activity_name', 'activity_id');

        return view('admin.history', compact('histories', 'activities'));
    }

    public function showBookingEdit($booking_id)
    {
        $booking = Bookings::findOrFail($booking_id);
        $institutes = Institutes::findOrFail($booking->institute_id);
        $visitors = Visitors::findOrFail($booking->visitor_id);
        $activities = Activity::all();
        $subactivities = Subactivity::where('activity_id', $booking->activity_id)
            ->where('status', 1)
            ->get();
        $timeslots = Timeslots::where('activity_id', $booking->activity_id)->get();
        $activity = Activity::findOrFail($booking->activity_id);
        $maxSubactivities = $activity->max_subactivities;

        return view('emails.visitorEditBooking', compact('booking', 'institutes', 'visitors', 'activities', 'subactivities', 'timeslots', 'maxSubactivities'));
    }

    public function showBookingAdminEdit($booking_id)
    {
        $booking = Bookings::findOrFail($booking_id);
        $institutes = Institutes::findOrFail($booking->institute_id);
        $visitors = Visitors::findOrFail($booking->visitor_id);
        $activities = Activity::all();
        $subactivities = Subactivity::where('activity_id', $booking->activity_id)
            ->where('status', 1)
            ->get();
        $timeslots = Timeslots::where('activity_id', $booking->activity_id)->get();
        $activity = Activity::findOrFail($booking->activity_id);
        $maxSubactivities = $activity->max_subactivities;

        return view('admin.AdminEditBooking', compact('booking', 'institutes', 'visitors', 'activities', 'subactivities', 'timeslots', 'maxSubactivities'));
    }

    public function updateBooking(Request $request, $booking_id)
    {
        $rules = [
            'fk_activity_id' => 'required|exists:activities,activity_id',
            'fk_timeslots_id' => 'nullable|exists:timeslots,timeslots_id',
            'sub_activity_id' => 'nullable|array',
            'sub_activity_id.*' => 'nullable|exists:sub_activities,sub_activity_id',
            'booking_date' => 'required|date_format:Y-m-d',
            'instituteName' => 'required',
            'instituteAddress' => 'required',
            'province' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'subdistrict' => 'required|string|max:100',
            'zipcode' => ['required', 'regex:/^[0-9]{5}$/'],
            'visitorName' => 'required',
            'visitorEmail' => 'required|email',
            'tel' => ['required', 'regex:/^[0-9]{10}$/'],
            'children_qty' => 'nullable|integer|min:0',
            'students_qty' => 'nullable|integer|min:0',
            'adults_qty' => 'nullable|integer|min:0',
            'kid_qty' => 'nullable|integer|min:0',
            'disabled_qty' => 'nullable|integer|min:0',
            'elderly_qty' => 'nullable|integer|min:0',
            'monk_qty' => 'nullable|integer|min:0',
            'note' => 'nullable|string'
        ];

        if (in_array($request->fk_activity_id, [1, 2, 3])) {
            $rules['fk_timeslots_id'] = 'required|exists:timeslots,timeslots_id';
        }
        $message = [
            'fk_timeslots_id.required' => 'กรุณาเลือกรอบการเข้าชม',
            'booking_date.required' => 'กรุณาระบุวันที่จองเข้าชม',
            'instituteName.required' => 'กรุณากรอกชื่อหน่วยงาน',
            'instituteAddress.required' => 'กรุณากรอกที่อยู่หน่วยงาน',
            'province.required' => 'กรุณากรอกจังหวัด',
            'district.required' => 'กรุณากรอกเขต/อำเภอ',
            'subdistrict.required' => 'กรุณากรอกแขวน/ตำบล',
            'zipcode.required' => 'กรุณกรอกรหัสไปรษณีย์',
            'visitorName.required' => 'กรุณกรอกชื่อผู้ประสานงาน',
            'visitorEmail.required' => 'กรุณกรอกอีเมล์ผู้ประสานงาน',
            'tel.required' => 'กรุณกรอกเบอร์โทรผู้ประสานงาน',
            'tel.regex' => 'กรุณกรอกเบอร์โทรในรูปแบบที่ถูกต้อง (10 หลัก)',
            'tel.starts_with' => 'เบอร์โทรต้องขึ้นต้นด้วย 0',
            'at_least_one_quantity.required' => 'กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท',
        ];

        $request->validate($rules, $message);

        $activity = Activity::find($request->fk_activity_id);
        if (!$activity) {
            return back()->with('error', 'ไม่พบกิจกรรม')->withInput();
        }
        $maxSubactivities = $activity->max_subactivities;
        $selectedSubactivities = $request->input('sub_activity_id', []);
        if (count($selectedSubactivities) > $maxSubactivities) {
            return back()->withErrors([
                'sub_activity_id' => "คุณสามารถเลือกได้สูงสุด $maxSubactivities กิจกรรมย่อยเท่านั้น"
            ])->withInput();
        }
        $quantityFields = [
            'children_qty',
            'students_qty',
            'adults_qty',
            'kid_qty',
            'disabled_qty',
            'elderly_qty',
            'monk_qty'
        ];

        $isAtLeastOneQuantityFilled = false;
        $totalToBook = 0;

        foreach ($quantityFields as $field) {
            if ($request->$field > 0) {
                $isAtLeastOneQuantityFilled = true;
            }
            $totalToBook += $request->$field ?? 0;
        }

        if (!$isAtLeastOneQuantityFilled) {
            return back()->withErrors([
                'at_least_one_quantity' => 'กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท'
            ])->withInput();
        }
        if ($totalToBook < 50) {
            session()->flash('error', 'กรุณาจองขั้นต่ำ 50 คน');
            return back()->withInput();
        }
        $activity = Activity::with('activityType')->find($request->fk_activity_id);
        if (!$activity) {
            return back()->with('error', 'ไม่พบกิจกรรม')->withInput();
        }

        $formattedDate = $request->booking_date;

        if ($request->filled('fk_timeslots_id')) {
            $timeslot = Timeslots::find($request->fk_timeslots_id);
            if (!$timeslot) {
                return back()->with('error', 'ไม่พบรอบการเข้าชม')->withInput();
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
                            $query->where('timeslots.start_time', '<', $timeslot->end_time)
                                ->where('timeslots.end_time', '>', $timeslot->start_time);
                        })
                        ->exists();

                    if ($conflictingBooking) {
                        return back()->with('error', 'ไม่สามารถจองกิจกรรมนี้ได้ เนื่องจากมีกิจกรรมที่จองในช่วงเวลาใกล้เคียงกันจากกิจกรรมอื่น')->withInput();
                    }
                }
            }
            if (in_array($activity->activity_id, [1, 2])) {
                if ($request->filled('fk_timeslots_id')) {
                    $timeslot = Timeslots::find($request->fk_timeslots_id);
                    if (!$timeslot) {
                        return back()->with('error', 'ไม่พบรอบการเข้าชม')->withInput();
                    }
                    $conflictingBooking = Bookings::join('timeslots', 'bookings.timeslots_id', '=', 'timeslots.timeslots_id')
                        ->where('bookings.activity_id', 3)
                        ->where('bookings.booking_date', $formattedDate)
                        ->where(function ($query) use ($timeslot) {
                            $query->where('timeslots.start_time', '<', $timeslot->end_time)
                                ->where('timeslots.end_time', '>', $timeslot->start_time);
                        })
                        ->exists();

                    if ($conflictingBooking) {
                        return back()->with('error', 'ไม่สามารถจองกิจกรรมนี้ได้ เนื่องจากมีการจองกิจกรรมอื่นในช่วงเวลาใกล้เคียง')->withInput();
                    }
                }
            }
            $totalToBook = ($request->children_qty ?? 0)
                + ($request->students_qty ?? 0)
                + ($request->adults_qty ?? 0)
                + ($request->kid_qty ?? 0)
                + ($request->disabled_qty ?? 0)
                + ($request->elderly_qty ?? 0)
                + ($request->monk_qty ?? 0);
            if ($totalToBook < 50) {
                return back()->withErrors([
                    'minimum_attendees' => 'กรุณาจองจำนวนผู้เข้าชมอย่างน้อย 50 คน'
                ])->withInput();
            }
            $totalBooked = Bookings::where('booking_date', $formattedDate)
                ->where('timeslots_id', $timeslot->timeslots_id)
                ->whereIn('status', [0, 1])
                ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty + monk_qty'));
            if ($activity->max_capacity !== null && $totalBooked + $totalToBook > $activity->max_capacity) {
                return back()->with('error', 'จำนวนเกินความจุต่อรอบการเข้าชม')->withInput();
            }
        }

        $booking = Bookings::findOrFail($booking_id);

        $institute = Institutes::firstOrNew([
            'instituteName' => $request->instituteName,
        ]);
        if ($institute->exists) {
            $institute->instituteAddress = $request->instituteAddress;
            $institute->province = $request->province;
            $institute->district = $request->district;
            $institute->subdistrict = $request->subdistrict;
            $institute->zipcode = $request->zipcode;
            $institute->save();
        } else {
            $institute->save();
        }

        $visitor = Visitors::updateOrCreate(
            ['visitorEmail' => $request->visitorEmail],
            [
                'visitorName' => $request->visitorName,
                'tel' => $request->tel,
                'institute_id' => $institute->institute_id,
            ]
        );
        $booking->update([
            'fk_activity_id' => $request->fk_activity_id,
            'booking_date' => $formattedDate,
            'timeslots_id' => $request->fk_timeslots_id,
            'children_qty' => $request->children_qty ?? 0,
            'students_qty' => $request->students_qty ?? 0,
            'adults_qty' => $request->adults_qty ?? 0,
            'kid_qty' => $request->kid_qty ?? 0,
            'disabled_qty' => $request->disabled_qty ?? 0,
            'elderly_qty' => $request->elderly_qty ?? 0,
            'monk_qty' => $request->monk_qty ?? 0,
            'note' => $request->note ?? null
        ]);

        $booking->subactivities()->sync($request->sub_activity_id ?? []);

        return back()->with('showSuccessModal', true);
    }
    public function showCancel($booking_id)
    {
        $booking = Bookings::with('activity')->findOrFail($booking_id);

        $childrenPrice = $booking->children_qty * ($booking->activity->children_price ?? 0);
        $studentPrice = $booking->students_qty * ($booking->activity->student_price ?? 0);
        $adultPrice = $booking->adults_qty * ($booking->activity->adult_price ?? 0);
        $kidPrice = $booking->kid_qty * ($booking->activity->kid_price ?? 0);
        $disabledPrice = $booking->disabled_qty * ($booking->activity->disabled_price ?? 0);
        $elderlyPrice = $booking->elderly_qty * ($booking->activity->elderly_price ?? 0);
        $monkPrice = $booking->monk_qty * ($booking->activity->monk_price ?? 0);
        $totalPrice = $childrenPrice + $studentPrice + $adultPrice + $kidPrice + $disabledPrice + $elderlyPrice + $monkPrice;

        return view('emails.ShowCancelledBooking', compact('booking', 'totalPrice'));
    }
    public function cancel(Request $request, $booking_id)
    {
        $booking = Bookings::findOrFail($booking_id);
        $booking->status = 3;
        $booking->save();

        $visitorEmail = $booking->visitor ? $booking->visitor->visitorEmail : null;
        if ($visitorEmail) {
            Mail::to($visitorEmail)->send(new BookingCancelledMail($booking));
        } else {
            Log::warning("ไม่พบอีเมลสำหรับการจองหมายเลข {$booking->booking_id}");
        }
        return back()->with('showSuccessModal', true);
    }
    public function showDetails($booking_id)
    {
        $booking = Bookings::findOrFail($booking_id);
        return view('emails.bookingDetails', compact('booking'));
    }
}
