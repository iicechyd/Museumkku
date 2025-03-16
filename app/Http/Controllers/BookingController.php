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
use App\Models\Tmss;
use App\Models\Activity;
use App\Models\SubActivity;
use App\Models\Institutes;
use App\Models\Visitors;
use App\Models\StatusChanges;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingApprovedMail;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingPendingMail;
use App\Http\Controllers\TmssController;

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

        $query = Bookings::with('activity', 'tmss', 'visitor', 'institute', 'documents', 'subactivities')
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

            if ($item->tmss && $item->tmss->tmss_id) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('tmss_id', $item->tmss->tmss_id)
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
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice + $kidPrice + $disabledPrice + $elderlyPrice + $monkPrice;
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

        $query = Bookings::with('activity', 'tmss', 'visitor', 'institute', 'subactivities')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
            ->where('status', 0);

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        $requestBookings = $query->paginate(5);

        foreach ($requestBookings as $item) {
            $totalApproved = Bookings::where('booking_date', $item->booking_date)
                ->where('activity_id', $item->activity_id)
                ->where('status', 1)
                ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty +  monk_qty'));

            if ($item->tmss && $item->tmss->tmss_id) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('tmss_id', $item->tmss->tmss_id)
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
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice + $kidPrice + $disabledPrice + $elderlyPrice + $monkPrice;
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

        $query = Bookings::with('activity', 'tmss', 'visitor', 'institute', 'documents', 'subactivities')
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

            if ($item->tmss && $item->tmss->tmss_id) {
                $totalApproved = Bookings::where('booking_date', $item->booking_date)
                    ->where('activity_id', $item->activity_id)
                    ->where('tmss_id', $item->tmss->tmss_id)
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
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice + $kidPrice + $disabledPrice + $elderlyPrice + $monkPrice;
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

        $query = Bookings::with('activity', 'tmss', 'visitor', 'institute', 'subactivities')
            ->whereHas('activity', function ($query) {
                $query->where('activity_type_id', 1);
            })
            ->where('status', 3);

        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }
        $exceptBookings = $query->paginate(5);

        foreach ($exceptBookings as $item) {
            $childrenPrice = $item->children_qty * $item->activity->children_price;
            $studentPrice = $item->students_qty * $item->activity->student_price;
            $adultPrice = $item->adults_qty * $item->activity->adult_price;
            $kidPrice = $item->kid_qty * $item->activity->kid_price;
            $disabledPrice = $item->disabled_qty * $item->activity->disabled_price;
            $elderlyPrice = $item->elderly_qty * $item->activity->elderly_price;
            $monkPrice = $item->monk_qty * $item->activity->monk_price;
            $item->totalPrice = $childrenPrice + $studentPrice + $adultPrice + $kidPrice + $disabledPrice + $elderlyPrice + $monkPrice;
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
            'actual_free_teachers_qty' => 'integer|min:0',
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
        $changedBy = Auth::user() ? Auth::user()->name : 'ผู้จองเข้าชม';

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
            $statusChange->actual_free_teachers_qty = $request->input('actual_free_teachers_qty', 0);
            $statusChange->changed_by = $changedBy;
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
                'actual_free_teachers_qty' => $request->input('actual_free_teachers_qty', 0),
                'changed_by' => $changedBy,
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
            'fk_tmss_id' => 'nullable|exists:tmss,tmss_id',
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
            $rules['fk_tmss_id'] = 'required|exists:tmss,tmss_id';
        } else {
            $rules['fk_tmss_id'] = 'nullable|exists:tmss,tmss_id';
        }
        $messages = [
            'fk_tmss_id.required' => 'กรุณาเลือกรอบการเข้าชม',
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

        $activity = Activity::with('activityType')->find($request->fk_activity_id);
        if (!$activity) {
            return back()->with('error', 'ไม่พบประเภทกิจกรรม')->withInput();
        }

        $maxCapacity = $activity->max_capacity ?? null;

        $tmss = Tmss::find($request->fk_tmss_id);
        if (!$tmss && !is_null($request->fk_tmss_id)) {
            return back()->with('error', 'ไม่พบรอบการเข้าชม')->withInput();
        }
        $maxSubactivities = $activity->max_subactivities;
        $selectedSubactivities = $request->input('sub_activity_id', []);
        if (count($selectedSubactivities) > $maxSubactivities) {
            return back()->withErrors([
                'sub_activity_id' => "คุณสามารถเลือกได้สูงสุด $maxSubactivities หลักสูตรเท่านั้น"
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
        $totalToBook = array_sum(array_map(fn($field) => $request->input($field, 0), $quantityFields));
        $isAtLeastOneQuantityFilled = collect($quantityFields)->some(fn($field) => $request->input($field, 0) > 0);

        if (!$isAtLeastOneQuantityFilled) {
            return back()->withErrors([
                'at_least_one_quantity' => 'กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท'
            ])->withInput();
        }
        if (!in_array($request->fk_activity_id, [1, 2, 3]) && $totalToBook < 50) {
            session()->flash('error', 'กรุณาจองขั้นต่ำ 50 คน');
            return back()->withInput();
        }

        $bookingDate = DateTime::createFromFormat('d/m/Y', $request->booking_date);
        if (!$bookingDate) {
            return back()->with('error', 'รูปแบบวันที่ไม่ถูกต้อง')->withInput();
        }
        $formattedDate = $bookingDate->format('Y-m-d');

        if (is_null($request->fk_tmss_id) && is_null($maxCapacity)) {
        } elseif (is_null($request->fk_tmss_id) && !is_null($maxCapacity)) {
            if ($totalToBook > $maxCapacity) {
                return back()->with('error', 'จำนวนเกินความจุต่อรอบการเข้าชม')->withInput();
            }
        } else {
            $tmssController = new TmssController();
            $availableTmss = json_decode($tmssController->getAvailableTmss($request->fk_activity_id, $formattedDate)->getContent());
            $selectedTmss = collect($availableTmss)->firstWhere('tmss_id', $request->fk_tmss_id);
            if (!$selectedTmss || $selectedTmss->remaining_capacity < $totalToBook) {
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
        $booking->tmss_id = $request->fk_tmss_id ?? null;
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
        session()->forget('visitor_data');
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

        $tmss = Tmss::where('activity_id', $activity_id)->get();
        $subactivities = SubActivity::where('activity_id', $activity_id)
            ->where('status', 1)
            ->get();
        $hasSubactivities = $subactivities->isNotEmpty();

        $email = session('verification_email');
        $visitor = Visitors::where('visitorEmail', $email)->with('institute')->first();

        $visitorData = [
            'visitorName' => $visitor->visitorName ?? '',
            'visitorEmail' => $visitor->visitorEmail ?? '',
            'tel' => $visitor->tel ?? '',
            'instituteName' => $visitor->institute->instituteName ?? '',
            'instituteAddress' => $visitor->institute->instituteAddress ?? '',
            'province' => $visitor->institute->province ?? '',
            'district' => $visitor->institute->district ?? '',
            'subdistrict' => $visitor->institute->subdistrict ?? '',
            'zipcode' => $visitor->institute->zipcode ?? '',
        ];

        return view('form_bookings', [
            'activity_id' => $activity_id,
            'selectedActivity' => $selectedActivity,
            'tmss' => $tmss,
            'subactivities' => $subactivities,
            'hasSubactivities' => $hasSubactivities,
            'maxSubactivities' => $selectedActivity->max_subactivities,
            'visitorData' => $visitorData,
        ]);
    }

    function WalkinBooking(Request $request)
    {
        $rules = [
            'fk_activity_id' => 'required|exists:activities,activity_id',
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

        $messages = [
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

        $activity = Activity::with('activityType')->find($request->fk_activity_id);
        if (!$activity) {
            return back()->with('error', 'ไม่พบประเภทกิจกรรม')->withInput();
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

        $formattedDate = Carbon::createFromFormat('d/m/Y', $request->input('booking_date'))->format('Y-m-d');
        $visitor = Visitors::where('visitorEmail', $request->input('visitorEmail'))->first();

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
        $booking->tmss_id = null;
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
        $booking->note = $request->note;
        $booking->status = 1;
        $booking->save();

        if ($request->has('sub_activity_id')) {
            $subActivities = $request->input('sub_activity_id');
            $booking->subActivities()->sync($subActivities);
        }
        session()->forget('visitor_data');
        $uploadLink = route('documents.upload', ['booking_id' => $booking->booking_id]);
        Mail::to($request->visitorEmail)->send(new BookingApprovedMail($booking, $uploadLink));
        return back()->with('showSuccessModal', true);
    }

    public function showAdminBookingForm($activity_id)
    {
        if (!session()->has('verification_email')) {
            session(['redirect_url' => route('admin_bookings.activity', ['activity_id' => $activity_id])]);
            return redirect()->route('guest.verify');
        }

        $selectedActivity = Activity::find($activity_id);
        if (!$selectedActivity) {
            return redirect()->back()->with('error', 'Activity not found.');
        }

        $tmss = Tmss::where('activity_id', $activity_id)->get();
        $subactivities = SubActivity::where('activity_id', $activity_id)
            ->where('status', 1)
            ->get();
        $hasSubactivities = $subactivities->isNotEmpty();

        $email = session('verification_email');
        $visitor = Visitors::where('visitorEmail', $email)->with('institute')->first();

        $visitorData = [
            'visitorName' => $visitor->visitorName ?? '',
            'visitorEmail' => $visitor->visitorEmail ?? '',
            'tel' => $visitor->tel ?? '',
            'instituteName' => $visitor->institute->instituteName ?? '',
            'instituteAddress' => $visitor->institute->instituteAddress ?? '',
            'province' => $visitor->institute->province ?? '',
            'district' => $visitor->institute->district ?? '',
            'subdistrict' => $visitor->institute->subdistrict ?? '',
            'zipcode' => $visitor->institute->zipcode ?? '',
        ];

        return view('admin.admin_bookings', [
            'activity_id' => $activity_id,
            'selectedActivity' => $selectedActivity,
            'tmss' => $tmss,
            'subactivities' => $subactivities,
            'hasSubactivities' => $hasSubactivities,
            'maxSubactivities' => $selectedActivity->max_subactivities,
            'visitorData' => $visitorData,
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
            'tmss',
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

        if ($request->filled('daily') && $request->daily == 'true') {
            $query->whereDate('booking_date', Carbon::today());
        }

        if ($request->filled('monthly') && $request->monthly == 'true') {
            $query->whereMonth('booking_date', Carbon::now()->month)
                ->whereYear('booking_date', Carbon::now()->year);
        }
        if ($request->filled('date_range') && !$request->filled('daily') && !$request->filled('monthly') && !$request->filled('fiscal_year')) {
            $dates = explode(" to ", $request->date_range);

            if (count($dates) === 2) {
                $startDate = $dates[0];
                $endDate = $dates[1];

                $query->whereBetween('booking_date', [$startDate, $endDate]);
            }
        }

        if ($request->filled('fiscal_year')) {
            $currentMonth = Carbon::now()->month;
            $currentYear  = Carbon::now()->year;

            if ($currentMonth < 10) {
                $startFiscalYear = Carbon::createFromDate($currentYear - 1, 10, 1)->startOfDay();
                $endFiscalYear   = Carbon::createFromDate($currentYear, 9, 30)->endOfDay();
            } else {
                $startFiscalYear = Carbon::createFromDate($currentYear, 10, 1)->startOfDay();
                $endFiscalYear   = Carbon::createFromDate($currentYear + 1, 9, 30)->endOfDay();
            }

            $query->whereBetween('booking_date', [$startFiscalYear, $endFiscalYear]);
        }
        $histories = $query->paginate(5);
        $totalBookings = $query->count();
        $totalBookedVisitors = $query->sum(DB::raw("
        COALESCE(children_qty, 0) +
        COALESCE(students_qty, 0) +
        COALESCE(adults_qty, 0) +
        COALESCE(kid_qty, 0) +
        COALESCE(disabled_qty, 0) +
        COALESCE(elderly_qty, 0) +
        COALESCE(monk_qty, 0)
    "));

        $totalRevenue = 0;
        $totalActualVisitors = 0;
        $priceDetailsByBooking = [];
        $priceDetails = [];

        $categories = [
            'children' => 'เด็ก',
            'students' => 'มัธยม / นักศึกษา',
            'adults' => 'ผู้ใหญ่ / คุณครู',
            'kid' => 'เด็กเล็ก',
            'disabled' => 'ผู้พิการ',
            'elderly' => 'ผู้สูงอายุ',
            'monk' => 'พระภิกษุสงฆ์ /สามเณร',
        ];

        foreach ($histories as $booking) {
            if (!$booking->activity) continue;

            $prices = [
                'children' => $booking->activity->children_price ?? 0,
                'students' => $booking->activity->student_price ?? 0,
                'adults' => $booking->activity->adult_price ?? 0,
                'kid' => $booking->activity->children_price ?? 0,
                'disabled' => $booking->activity->disabled_price ?? 0,
                'elderly' => $booking->activity->elderly_price ?? 0,
                'monk' => $booking->activity->monk_price ?? 0
            ];

            foreach ($booking->statusChanges as $statusChange) {
                $totalParticipants = 0;
                $totalPrice = 0;
                $priceDetails = [];

                foreach ($categories as $key => $label) {
                    $qtyField = "actual_{$key}_qty";
                    $qty = $statusChange->$qtyField ?? 0;
                    $price = $prices[$key] ?? 0;
                    $total = $qty * $price;

                    if ($qty > 0) {
                        $priceDetails[] = [
                            'label' => $label,
                            'qty' => $qty,
                            'price' => $price,
                            'total' => $total
                        ];
                    }

                    $totalParticipants += $qty;
                    $totalPrice += $total;
                }
                $totalParticipants += $statusChange->actual_free_teachers_qty ?? 0;
                $totalRevenue += $totalPrice;
                $totalActualVisitors += $totalParticipants;

                $priceDetailsByBooking[$statusChange->booking_id] = [
                    'details' => $priceDetails,
                    'totalParticipants' => $totalParticipants,
                    'totalPrice' => $totalPrice
                ];
            }
        }
        $activities = Activity::orderBy('activity_name')->pluck('activity_name', 'activity_id');

        return view('admin.history', compact('histories', 'activities', 'totalRevenue', 'totalBookings', 'totalBookedVisitors', 'totalActualVisitors', 'priceDetailsByBooking'));
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
        $tmss = Tmss::where('activity_id', $booking->activity_id)->get();
        $activity = Activity::findOrFail($booking->activity_id);
        $maxSubactivities = $activity->max_subactivities;

        return view('emails.visitorEditBooking', compact('booking', 'institutes', 'visitors', 'activities', 'subactivities', 'tmss', 'maxSubactivities'));
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
        $tmss = Tmss::where('activity_id', $booking->activity_id)->get();
        $activity = Activity::findOrFail($booking->activity_id);
        $maxSubactivities = $activity->max_subactivities;

        return view('admin.AdminEditBooking', compact('booking', 'institutes', 'visitors', 'activities', 'subactivities', 'tmss', 'maxSubactivities'));
    }

    public function updateBooking(Request $request, $booking_id)
    {
        $rules = [
            'fk_activity_id' => 'required|exists:activities,activity_id',
            'fk_tmss_id' => 'nullable|exists:tmss,tmss_id',
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

        if (in_array($request->fk_activity_id, [1, 2, 3]) && $request->note !== 'วอคอิน') {
            $rules['fk_tmss_id'] = 'required|exists:tmss,tmss_id';
        } else {
            $rules['fk_tmss_id'] = 'nullable|exists:tmss,tmss_id';
        }
        $message = [
            'fk_tmss_id.required' => 'กรุณาเลือกรอบการเข้าชม',
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

        $activity = Activity::with('activityType')->find($request->fk_activity_id);
        if (!$activity) {
            return back()->with('error', 'ไม่พบประเภทกิจกรรม')->withInput();
        }

        $maxCapacity = $activity->max_capacity ?? null;

        $tmss = Tmss::find($request->fk_tmss_id);
        if (!$tmss && !is_null($request->fk_tmss_id)) {
            return back()->with('error', 'ไม่พบรอบการเข้าชม')->withInput();
        }

        $maxSubactivities = $activity->max_subactivities;
        $selectedSubactivities = $request->input('sub_activity_id', []);
        if (count($selectedSubactivities) > $maxSubactivities) {
            return back()->withErrors([
                'sub_activity_id' => "คุณสามารถเลือกได้สูงสุด $maxSubactivities กิจกรรมย่อยเท่านั้น"
            ])->withInput();
        }
        $quantityFields = [
            'children_qty', 'students_qty', 'adults_qty', 'kid_qty',
            'disabled_qty', 'elderly_qty', 'monk_qty'
        ];

        $isAtLeastOneQuantityFilled = false;
        $totalToBook = 0;

        foreach ($quantityFields as $field) {
            if ($request->$field > 0) {
                $isAtLeastOneQuantityFilled = true;
            }
            $totalToBook += $request->$field ?? 0;
        }

        if ($request->note !== 'วอคอิน') {
            if (!$isAtLeastOneQuantityFilled) {
                return back()->withErrors([
                    'at_least_one_quantity' => 'กรุณาระบุจำนวนผู้เข้าชมอย่างน้อย 1 ประเภท'
                ])->withInput();
            }
            if (!in_array($request->fk_activity_id, [1, 2, 3]) && $totalToBook < 50) {
                session()->flash('error', 'กรุณาจองขั้นต่ำ 50 คน');
                return back()->withInput();
            }
        }
        $formattedDate = $request->booking_date;
        $booking = Bookings::findOrFail($booking_id);

        $previousTotal = $booking->children_qty + $booking->students_qty + $booking->adults_qty +
            $booking->kid_qty + $booking->disabled_qty + $booking->elderly_qty +
            $booking->monk_qty;

            if ($request->note !== 'วอคอิน' && is_null($request->fk_tmss_id) && !is_null($maxCapacity)) {
                if ($totalToBook > $maxCapacity) {
                return back()->with('error', 'จำนวนเกินความจุต่อรอบการเข้าชม')->withInput();
            }
        } 
        if ($request->note !== 'วอคอิน' && $request->fk_tmss_id) {
            $tmssController = new TmssController();
            $availableTmss = json_decode($tmssController->getAvailableTmss($request->fk_activity_id, $formattedDate)->getContent());
            $selectedTmss = collect($availableTmss)->firstWhere('tmss_id', $request->fk_tmss_id);

            if ($selectedTmss) {
                $adjustedRemainingCapacity = $selectedTmss->remaining_capacity + $previousTotal;
                if ($adjustedRemainingCapacity < $totalToBook) {
                    return back()->with('error', 'จำนวนเกินความจุต่อรอบการเข้าชม')->withInput();
                }
            } else {
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
            'tmss_id' => $request->note === 'วอคอิน' ? null : $request->fk_tmss_id,
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
    public function showDetails($booking_id)
    {
        $booking = Bookings::findOrFail($booking_id);
        $statusChange = $booking->note === 'วอคอิน'
            ? StatusChanges::where('booking_id', $booking_id)->latest()->first()
            : null;

        $quantities = [
            'children_qty' => $statusChange ? $statusChange->actual_children_qty : $booking->children_qty,
            'students_qty' => $statusChange ? $statusChange->actual_students_qty : $booking->students_qty,
            'adults_qty' => $statusChange ? $statusChange->actual_adults_qty : $booking->adults_qty,
            'kid_qty' => $statusChange ? $statusChange->actual_kid_qty : $booking->kid_qty,
            'disabled_qty' => $statusChange ? $statusChange->actual_disabled_qty : $booking->disabled_qty,
            'elderly_qty' => $statusChange ? $statusChange->actual_elderly_qty : $booking->elderly_qty,
            'monk_qty' => $statusChange ? $statusChange->actual_monk_qty : $booking->monk_qty,
            'free_teachers_qty' => $statusChange ? $statusChange->actual_free_teachers_qty : 0,
        ];

        $totalPrice =
            ($quantities['children_qty'] * $booking->activity->children_price) +
            ($quantities['students_qty'] * $booking->activity->student_price) +
            ($quantities['adults_qty'] * $booking->activity->adult_price) +
            ($quantities['kid_qty'] * $booking->activity->kid_price) +
            ($quantities['disabled_qty'] * $booking->activity->disabled_price) +
            ($quantities['elderly_qty'] * $booking->activity->elderly_price) +
            ($quantities['monk_qty'] * $booking->activity->monk_price);

        return view('emails.bookingDetails', compact('booking', 'quantities', 'totalPrice'));
    }
}
