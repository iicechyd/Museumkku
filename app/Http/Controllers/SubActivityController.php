<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubActivity;
use App\Models\Activity;
use App\Models\Bookings;

class SubActivityController extends Controller
{
    public function showSubActivities()
    {
        $activities = Activity::all();
        $subActivities = SubActivity::with('activity')->get();
        return view('admin.subactivity_list', compact('subActivities', 'activities'));
    }
    public function storeSubActivity(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'sub_activity_name' => 'required|string|max:255',
        ]);

        $subActivity = new SubActivity();
        $subActivity->activity_id = $request->activity_id;
        $subActivity->sub_activity_name = $request->sub_activity_name;
        $subActivity->save();

        return redirect()->route('admin.subactivities')->with('success', 'เพิ่มหลักสูตรเรียบร้อยแล้ว');
    }
    public function toggleSubactivityStatus(Request $request, $subActivityId)
    {
        $subActivity = SubActivity::find($subActivityId);
        if ($subActivity) {
            $subActivity->status = $request->status;
            $subActivity->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
    public function updateMaxSubactivities(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'max_subactivities' => 'required|integer',
        ]);

        $activity = Activity::find($request->activity_id);
        $activity->max_subactivities = $request->max_subactivities;
        $activity->save();

        return response()->json(['success' => 'แก้ไขจำนวนหลักสูตรที่เลือกได้เรียบร้อยแล้ว']);
    }

    public function update(Request $request, $subActivityId)
    {
        $subActivity = SubActivity::findOrFail($subActivityId);
        $subActivity->sub_activity_name = $request->sub_activity_name;
        $subActivity->save();
    
        return back()->with('success', 'แก้ไขหลักสูตรสำเร็จ');
    }
    
    public function delete($id)
    {
        $hasPendingBookings = Bookings::whereHas('subActivities', function ($query) use ($id) {
            $query->where('booking_subactivities.sub_activity_id', $id);
        })
        ->whereIn('status', [0, 1])
        ->exists();
    
        if ($hasPendingBookings) {
            return redirect()->back()->with('error', 'ไม่สามารถลบหลักสูตรนี้ได้ เนื่องจากมีการจองหลักสูตรนี้ที่รอดำเนินการในระบบ');
        }
        $subActivity = SubActivity::findOrFail($id);
        $subActivity->delete();

        return redirect()->back()->with('success', 'ลบหลักสูตรสำเร็จ');
    }
}
