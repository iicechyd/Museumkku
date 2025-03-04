<?php

namespace App\Http\Controllers;

use App\Models\ActivityImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\ActivityType;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function showDetail($activity_id)
    {
        $activity = Activity::with('images')->findOrFail($activity_id);
        return view('activity_detail', compact('activity'));
    }

    public function previewActivity()
    {
        $activities = Activity::with('images')
            ->where('activity_type_id', 2)
            ->where('status', 'active')
            ->get();
        return view('preview_activity', compact('activities'));
    }

    public function previewGeneral()
    {
        $activities = Activity::with('images')
            ->where('activity_type_id', 1)
            ->where('status', 'active')
            ->get();
        return view('preview_general', compact('activities'));
    }

    public function AdminPreviewGeneral()
    {
        $activities = Activity::with('images')
            ->where('activity_type_id', 1)
            ->where('status', 'active')
            ->get();
        return view('admin.admin_preview_general', compact('activities'));
    }

    function showListActivity()
    {
        $allActivities = Activity::all();
        $requestListActivity = Activity::with(['activityType', 'images'])
        ->paginate(4);
        $activityTypes = ActivityType::all();
        return view('admin.activity_list', compact('requestListActivity', 'activityTypes', 'allActivities'));
    }

        function delete($activity_id)
        {
        $hasPendingBookings = DB::table('bookings')
        ->where('activity_id', $activity_id)
        ->whereIn('status', [0, 1])
        ->exists();

    if ($hasPendingBookings) {
        session()->flash('error', 'ไม่สามารถลบกิจกรรมนี้ได้ เนื่องจากมีการจองกิจกรรมนี้ที่รอดำเนินการในระบบ');
        return redirect('/admin/activity_list');
    }

    DB::table('activities')->where('activity_id', $activity_id)->delete();
    session()->flash('success', 'ลบกิจกรรมเรียบร้อยแล้ว');

            return redirect('/admin/activity_list');
        }

    public function deleteImage($image_id)
    {
        $image = ActivityImages::findOrFail($image_id);
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return redirect()->back()->with('success', 'ลบรูปภาพเรียบร้อยแล้ว');
    }

    function InsertActivity(Request $request)
    {
        $request->validate(
            [
                'activity_name' => 'required',
                'description' => 'required',
                'children_price' => 'required',
                'student_price' => 'required',
                'adult_price' => 'required',
                'kid_price' => 'required',
                'disabled_price' => 'required',
                'elderly_price' => 'required',
                'monk_price' => 'required',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'target_yearly_count' => 'nullable|integer',
            ],
            [
                'activity_name.required' => 'กรุณาป้อนชื่อกิจกรรม',
                'description.required' => 'กรุณาป้อนรายละเอียดกิจกรรม',
                'children_price.required' => 'กรุณาป้อนราคาเด็ก',
                'student_price.required' => 'กรุณาป้อนราคานร/นศ',
                'adult_price.required' => 'กรุณาป้อนราคาผู้ใหญ่',
                'kid_price.required' => 'กรุณาป้อนราคาเด็กเล็ก',
                'disabled_price.required' => 'กรุณาป้อนราคาผู้พิการ',
                'elderly_price.required' => 'กรุณาป้อนราคาผู้สูงอายุ',
                'monk_price.required' => 'กรุณาป้อนราคาพระภิกษุสงฆ์ /สามเณร',
                'images.*.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพ',
                'images.*.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpeg, png, jpg, หรือ gif',
            ]
        );

        $activity = new Activity();
        $activity->activity_name = $request->activity_name;
        $activity->description = $request->description;
        $activity->children_price = $request->children_price;
        $activity->student_price = $request->student_price;
        $activity->adult_price = $request->adult_price;
        $activity->kid_price = $request->kid_price;
        $activity->disabled_price = $request->disabled_price;
        $activity->elderly_price = $request->elderly_price;
        $activity->monk_price = $request->monk_price;
        $activity->max_capacity = $request->max_capacity;
        $activity->target_yearly_count = $request->target_yearly_count;
        $activity->activity_type_id = $request->activity_type_id;
        $activity->status = 'inactive';
        $activity->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $newFileName = "activity_{$activity->activity_id}_" . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs("activity_images/{$activity->activity_id}", $newFileName, 'public');

                ActivityImages::create([
                'activity_id' => $activity->activity_id,
                'image_path' => $imagePath,
            ]);
            }
        }
        return redirect('/admin/activity_list')->with('success', 'เพิ่มกิจกรรมสำเร็จ');
    }

    public function updateActivity(Request $request)
    {
        $activity = Activity::find($request->activity_id);
        if (!$activity) {
            return redirect()->back()->with('error', 'ไม่พบกิจกรรมที่ต้องการแก้ไข');
        }
        $activity->activity_type_id = $request->activity_type_id;
        $activity->activity_name = $request->activity_name;
        $activity->description = $request->description;
        $activity->children_price = $request->children_price;
        $activity->student_price = $request->student_price;
        $activity->adult_price = $request->adult_price;
        $activity->kid_price = $request->kid_price;
        $activity->disabled_price = $request->disabled_price;
        $activity->elderly_price = $request->elderly_price;
        $activity->monk_price = $request->monk_price;
        $activity->max_capacity = $request->max_capacity;
        $activity->target_yearly_count = $request->target_yearly_count;
        $activity->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $newFileName = "activity_{$activity->activity_id}_" . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs("activity_images/{$activity->activity_id}", $newFileName, 'public');

                ActivityImages::create([
                'activity_id' => $activity->activity_id,
                'image_path' => $imagePath,
            ]);
            }
        }
        return redirect()->back()->with('success', 'แก้ไขกิจกรรมสำเร็จ');
    }

    public function editActivity($activity_id)
    {
        $item = Activity::find($activity_id);
        return view('activity_list', [
            'activity' => $item,
        ]);
    }

    public function toggleStatus($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->status = ($activity->status === 'active') ? 'inactive' : 'active';
        $activity->save();
        return response()->json([
            'status' => $activity->status,
            'message' => 'สถานะของกิจกรรมถูกเปลี่ยนเรียบร้อยแล้ว'
        ]);
    }
    public function addTarget(Request $request)
    {
        $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'target_yearly_count' => 'required|integer',
        ]);

        $activity = Activity::find($request->activity_id);
        if ($activity) {
            $activity->target_yearly_count = $request->target_yearly_count;
            $activity->save();

            return redirect()->back()->with('success', 'เป้าหมายกิจกรรมถูกเพิ่มเรียบร้อยแล้ว');
        } else {
            return redirect()->back()->with('error', 'ไม่พบกิจกรรมที่ระบุ');
        }
    }
}
