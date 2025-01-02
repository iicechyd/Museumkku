<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Timeslots;
use App\Models\closedTimeslots;

class TimeslotController extends Controller
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

        return response()->json([
            'status' => $timeslot->status,
            'message' => 'สถานะของกิจกรรมถูกเปลี่ยนเรียบร้อยแล้ว'
        ]);
    }

    // public function getTimeslots(Request $request)
    // {
    //     $activityId = $request->input('activity_id');
    //     $bookingDate = $request->input('booking_date');

    //     $timeslots = Timeslots::where('activity_id', $activityId)
    //         ->whereDoesntHave('closedTimeslots', function ($query) use ($bookingDate) {
    //             $query->where('closed_on', $bookingDate);
    //         })
    //         ->where('status', 'active')
    //         ->select('timeslots_id', 'start_time', 'end_time', 'remaining_capacity')
    //         ->get();

    //     return response()->json($timeslots);
    // }

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
        $activities = Activity::all();

        $closedDates = ClosedTimeslots::with(['activity', 'timeslot'])
            ->select('closed_timeslots_id', 'activity_id', 'timeslots_id', 'closed_on')
            ->orderBy('closed_on', 'desc')
            ->get();

        return view('admin.manage_closed_dates', compact('activities', 'closedDates'));
    }

    public function getTimeslotsByActivity(Request $request)
    {
        $timeslots = Timeslots::where('activity_id', $request->activity_id)->get();
        return response()->json($timeslots);
    }

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
            ClosedTimeslots::create([
                'activity_id' => $activityId,
                'timeslots_id' => null,
                'closed_on' => $closedOn,
            ]);
        } else {
            ClosedTimeslots::create([
                'activity_id' => $activityId,
                'timeslots_id' => $timeslotsId,
                'closed_on' => $closedOn,
            ]);
        }

        return redirect()->back()->with('success', 'บันทึกข้อมูลการปิดรอบเรียบร้อยแล้ว');
    }
    public function deleteClosedDate($id)
    {
        try {
            $closedTimeslot = ClosedTimeslots::findOrFail($id);
            $closedTimeslot->delete();

            return redirect()->back()->with('success', 'ลบวันที่ปิดรอบสำเร็จ');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการลบวันที่ปิดรอบ');
        }
    }
}
