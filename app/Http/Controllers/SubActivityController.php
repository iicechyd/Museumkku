<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubActivity;
use App\Models\Activity;

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

        return redirect()->route('admin.subactivities')->with('success', 'เพิ่มกิจกรรมย่อยเรียบร้อยแล้ว');
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

}
