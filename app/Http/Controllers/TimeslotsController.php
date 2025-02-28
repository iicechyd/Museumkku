<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Timeslots;
use App\Models\Bookings;
use App\Models\closedTimeslots;
use Carbon\Carbon;
use DateTime;

class TimeslotsController extends Controller
{
    public function showTimeslots()
    {
        $activities = Activity::with('timeslots')->get();

        return view('admin.timeslots_list', compact('activities'));
    }

    public function update(Request $request, $id)
    {
        $timeslot = Timeslots::findOrFail($id);
        $timeslot->start_time = $request->input('start_time');
        $timeslot->end_time = $request->input('end_time');
        $timeslot->save();

        return redirect()->back()->with('success', 'แก้ไขรอบการเข้าชมเรียบร้อยแล้ว');
    }

    public function InsertTimeslots(Request $request)
    {
        $messages = [
            'end_time.after' => 'เวลาสิ้นสุดต้องช้ากว่าเวลาเริ่มต้น กรุณาเลือกเวลาใหม่อีกครั้ง',
        ];
        $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|after:start_time',
        ], $messages);

        $timeslot = new Timeslots();
        $timeslot->activity_id = $request->input('activity_id');
        $timeslot->start_time = $request->input('start_time');
        $timeslot->end_time = $request->input('end_time');
        $timeslot->save();

        return redirect()->back()->with('success', 'เพิ่มรอบการเข้าชมเรียบร้อยแล้ว');
    }

        public function delete($id)
    {
        $hasPendingBookings = Bookings::whereHas('timeslot', function ($query) use ($id) {
            $query->where('timeslots.timeslots_id', $id);
        })
        ->whereIn('status', [0, 1])
        ->exists();
    
        if ($hasPendingBookings) {
            return redirect()->back()->with('error', 'ไม่สามารถลบรอบการเข้าชมนี้ได้ เนื่องจากมีการจองรอบการเข้าชมของกิจกรรมนี้ที่รอดำเนินการในระบบ');
        }

        $timeslot = Timeslots::findOrFail($id);
        $timeslot->delete();

        return redirect()->back()->with('success', 'ลบรอบการเข้าชมเรียบร้อยแล้ว');
    }

    public function toggleStatus($id)
    {
        $timeslot = Timeslots::findOrFail($id);
        $timeslot->status = ($timeslot->status === 1) ? 0 : 1;
        $timeslot->save();

        return response()->json([
            'status' => $timeslot->status,
            'message' => 'สถานะของกิจกรรมถูกเปลี่ยนเรียบร้อยแล้ว'
        ]);
    }
    public function showClosedDates()
    {
        $activities = Activity::all();

        $closedDates = ClosedTimeslots::with(['activity', 'timeslot'])
            ->select('closed_timeslots_id', 'activity_id', 'timeslots_id', 'closed_on', 'comments')
            ->orderBy('closed_on', 'desc')
            ->get();

        return view('admin.manage_closed_dates', compact('activities', 'closedDates'));
    }

    public function saveClosedDates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activity_id' => 'required|exists:activities,activity_id',
            'timeslots_id' => 'required',
            'closed_on' => 'required|date_format:d/m/Y',
            'comments' => 'required|string|max:255',
        ], [
            'activity_id.required' => 'กรุณาเลือกประเภทการเข้าชม',
            'timeslots_id.required' => 'กรุณาเลือกรอบการเข้าชม',
            'closed_on.required' => 'กรุณาเลือกวันที่ปิดรอบการเข้าชม',
            'closed_on.date_format' => 'รูปแบบวันที่ต้องเป็น วัน/เดือน/ปี (เช่น 25/02/2025)',
            'comments.required' => 'กรุณากรอกหมายเหตุการปิดรอบการเข้าชม',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }    

        $activityId = $request->input('activity_id');
        $timeslotsId = $request->input('timeslots_id');

        $closedOn = DateTime::createFromFormat('d/m/Y', $request->closed_on);
        if (!$closedOn) {
            return back()->with('error', 'รูปแบบวันที่ไม่ถูกต้อง')->withInput();
        }
        $formattedDate = $closedOn->format('Y-m-d');
        $comments = $request->input('comments');

        $existingAllClosed = ClosedTimeslots::where('activity_id', $activityId)
        ->whereNull('timeslots_id')
        ->where('closed_on', $formattedDate)
        ->exists();
        
        if ($existingAllClosed && $timeslotsId !== 'all') {
            return redirect()->back()->with('error', 'ไม่สามารถบันทึกข้อมูลได้ เนื่องจากมีการปิดทุกรอบแล้ว')->withInput();
        }

        $existingClosedTimeslot = ClosedTimeslots::where('activity_id', $activityId)
        ->where('closed_on', $formattedDate)
        ->when($timeslotsId !== 'all', function ($query) use ($timeslotsId) {
            return $query->where('timeslots_id', $timeslotsId);
        })
        ->exists();

        if ($existingClosedTimeslot) {
            return redirect()->back()->with('error', 'ไม่สามารถบันทึกข้อมูลซ้ำได้')->withInput();
        }

        ClosedTimeslots::create([
            'activity_id' => $activityId,
            'timeslots_id' => $timeslotsId === 'all' ? null : $timeslotsId,
            'closed_on' => $formattedDate,
            'comments' => $comments,
        ]);

        return redirect()->back()->with('success', 'บันทึกข้อมูลการปิดรอบการเข้าชมเรียบร้อยแล้ว');
    }
    public function deleteClosedDate($id)
    {
        try {
            $closedTimeslot = ClosedTimeslots::findOrFail($id);
            $closedTimeslot->delete();

            return redirect()->back()->with('success', 'ยกเลิกวันที่ปิดรอบการเข้าชมสำเร็จ');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการลบวันที่ปิดรอบการเข้าชม');
        }
    }

    public function getTimeslotsByActivity(Request $request)
    {
        $timeslots = Timeslots::where('activity_id', $request->activity_id)
            ->get()
            ->map(function ($timeslot) {
                $timeslot->start_time = Carbon::parse($timeslot->start_time)->format('H:i') . ' น.';
                $timeslot->end_time = Carbon::parse($timeslot->end_time)->format('H:i') . ' น.';
                return $timeslot;
            });
        return response()->json($timeslots);
    }
    public function getAvailableTimeslots($activity_id, $date)
    {
        $closedTimeslotsIds = ClosedTimeslots::where('closed_on', $date)
            ->where('activity_id', $activity_id)
            ->pluck('timeslots_id');

        $availableTimeslots = Timeslots::where('activity_id', $activity_id)
            ->where('status', 1)    
            ->whereNotIn('timeslots_id', $closedTimeslotsIds)
            ->get();

        $availableTimeslotsWithCapacity = $availableTimeslots->map(function ($timeslot) use ($activity_id, $date) {
            $totalApproved = Bookings::where('booking_date', $date)
                ->where('activity_id', $activity_id)
                ->where('timeslots_id', $timeslot->timeslots_id)
                ->whereIn('status', [0, 1])
                ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty + monk_qty'));

            if ($timeslot->activity->max_capacity !== null) {
                $remainingCapacity = $timeslot->activity->max_capacity - $totalApproved;
            } else {
                $remainingCapacity = 'ไม่จำกัดจำนวนคน';
            }

            $timeslot->remaining_capacity = $remainingCapacity;
            return $timeslot;
        });
        return response()->json($availableTimeslotsWithCapacity);
    }
}
