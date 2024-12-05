<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Timeslots;

class TimeslotController extends Controller
{
    public function showTimeslots()
    {
        $activities = Activity::with('timeslots')->get();

        return view('admin.timeslots_list', compact('activities'));
    }

    public function fetchTimeslots(Request $request)
    {
        $activityId = $request->input('fk_activity_id');
        $bookingDate = $request->input('booking_date');

        if (!$activityId || !$bookingDate) {
            return response()->json(['error' => 'กรุณาระบุ activity_id และ booking_date'], 400);
        }
        
        // Fetch available timeslots with remaining capacity
        $timeslots = Timeslots::where('activity_id', $activityId)
        ->whereDoesntHave('closedTimeslots', function ($query) use ($bookingDate) {
            $query->where('closed_on', $bookingDate);
        })
        ->where('max_capacity', '>', DB::raw('booked'))
        ->get();

        return response()->json($timeslots);
    }

    public function update(Request $request, $id)
    {
        $timeslot = Timeslots::findOrFail($id);
        $timeslot->start_time = $request->input('start_time');
        $timeslot->end_time = $request->input('end_time');
        $timeslot->save();

        return redirect()->back()->with('success', 'แก้ไขรอบการเข้าชมเรียบร้อยแล้ว!');
    }

    public function InsertTimeslots(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|after:start_time',
        ]);

        $timeslot = new Timeslots();
        $timeslot->activity_id = $request->input('activity_id');
        $timeslot->start_time = $request->input('start_time');
        $timeslot->end_time = $request->input('end_time');
        $timeslot->save();

        return redirect()->back()->with('success', 'เพิ่มรอบการเข้าชมเรียบร้อยแล้ว');
    }

    public function toggleStatus($id)
{
    $timeslot = Timeslots::findOrFail($id);
    $timeslot->status = ($timeslot->status === 'active') ? 'inactive' : 'active';
    $timeslot->save();

    // ส่งข้อมูลกลับเป็น JSON
    return response()->json([
        'status' => $timeslot->status,
        'message' => 'สถานะของกิจกรรมถูกเปลี่ยนเรียบร้อยแล้ว'
    ]);
}

public function getTimeslots(Request $request)
{
    $activityId = $request->input('activity_id');
    $bookingDate = $request->input('booking_date');

    $timeslots = Timeslots::where('activity_id', $activityId)
    ->whereDoesntHave('closedTimeslots', function ($query) use ($bookingDate) {
        $query->where('closed_on', $bookingDate);
    })
    ->where('status', 'active')
    ->get();

    return response()->json($timeslots);
}

public function closeTimeslot(Request $request, $timeslotId)
{
    $request->validate([
        'closed_on' => 'required|date',
    ]);

    DB::table('closed_timeslots')->insert([
        'timeslot_id' => $timeslotId,
        'closed_on' => $request->input('closed_on'),
    ]);

    return redirect()->back()->with('success', 'ปิดรอบการเข้าชมสำเร็จแล้ว');
}
public function showClosedDates()
{
    $activities = Activity::all(); // ดึงข้อมูลกิจกรรมทั้งหมด

    // ดึงข้อมูลวันที่ปิดรอบจากตาราง closed_timeslots
    $closedDates = DB::table('closed_timeslots')
        ->join('activities', 'closed_timeslots.activity_id', '=', 'activities.activity_id')
        ->leftJoin('timeslots', 'closed_timeslots.timeslots_id', '=', 'timeslots.timeslots_id')
        ->select(
            'activities.activity_name',
            'timeslots.start_time',
            'timeslots.end_time',
            'closed_timeslots.closed_on'
        )
        ->orderBy('closed_on', 'desc')
        ->get();

    return view('admin.manage_closed_dates', compact('activities', 'closedDates'));
}


public function getTimeslotsByActivity(Request $request)
{
    $timeslots = Timeslots::where('activity_id', $request->activity_id)->get(); // ดึง Timeslots ที่เกี่ยวข้องกับกิจกรรม
    return response()->json($timeslots);
}

    // บันทึกวันปิด
    public function saveClosedDates(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'timeslots_id' => 'required',
            'closed_on' => 'required|date',
        ]);
    
        $activityId = $request->input('activity_id');
        $timeslotsId = $request->input('timeslots_id');
        $closedOn = $request->input('closed_on');
    
        if ($timeslotsId === 'all') {
            // ปิดทุกรอบของกิจกรรมในวันนั้น
            $timeslots = Timeslots::where('activity_id', $activityId)->pluck('timeslots_id');

            foreach ($timeslots as $id) {
                DB::table('closed_timeslots')->insert([
                    'activity_id' => $activityId,
                    'timeslots_id' => $id,
                    'closed_on' => $closedOn,
                ]);
            }
        } else {
            DB::table('closed_timeslots')->insert([
                'activity_id' => $activityId,
                'timeslots_id' => $timeslotsId,
                'closed_on' => $closedOn,
            ]);
        }
    
        return redirect()->back()->with('success', 'บันทึกข้อมูลการปิดรอบเรียบร้อยแล้ว');
    }
    
}
