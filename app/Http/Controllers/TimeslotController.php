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
        // Retrieve all activities along with their related timeslots
        $activities = Activity::with('timeslots')->get();

        return view('admin.timeslots_list', compact('activities'));
    }
    public function getTimeslots(Request $request)
    {
        $activityId = $request->get('activity_id');
        $timeslots = Timeslots::where('activity_id', $activityId)->get();

        return response()->json($timeslots);
    }

    public function fetchTimeslots(Request $request)
    {
        $activityId = $request->input('fk_activity_id');
        $bookingDate = $request->input('booking_date');

        // Fetch available timeslots with remaining capacity
        $timeslots = Timeslots::where('activity_id', $activityId)
            ->where('date', $bookingDate)
            ->where('max_capacity', '>', DB::raw('booked'))
            ->get();

        return response()->json($timeslots);
    }

    public function destroy($id)
    {
        // ค้นหา timeslot ที่ต้องการลบ
        $timeslot = Timeslots::findOrFail($id);

        // ลบ timeslot
        $timeslot->delete();

        // ส่งข้อความแจ้งเตือนความสำเร็จ
        return redirect()->back()->with('success', 'รอบการเข้าชมถูกลบเรียบร้อยแล้ว');
    }

    public function update(Request $request, $id)
    {
        $timeslot = Timeslots::findOrFail($id);
        $timeslot->start_time = $request->input('start_time');
        $timeslot->end_time = $request->input('end_time');
        $timeslot->max_capacity = $request->input('max_capacity');
        $timeslot->save();

        return redirect()->back()->with('success', 'แก้ไขรอบการเข้าชมเรียบร้อยแล้ว!');
    }

    public function InsertTimeslots(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'max_capacity' => 'required|integer|min:1',
        ]);

        $timeslot = new Timeslots();
        $timeslot->activity_id = $request->input('activity_id');
        $timeslot->start_time = $request->input('start_time');
        $timeslot->end_time = $request->input('end_time');
        $timeslot->max_capacity = $request->input('max_capacity');
        $timeslot->save();

        return redirect()->back()->with('success', 'เพิ่มรอบการเข้าชมเรียบร้อยแล้ว');
    }
}
