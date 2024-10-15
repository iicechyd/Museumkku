<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\ActivityType;

class ActivityController extends Controller
{
    public function showDetail($activity_id)
    {
        $activity = Activity::findOrFail($activity_id);
        return view('activity_detail', compact('activity')); // Pass the single activity
    }

    public function index()
    {
        $activities = Activity::all();
        return view('welcome', compact('activities'));
    }

    public function previewActivity()
    {
        $activities = Activity::where('activity_type_id', 2)->get();
        return view('preview_activity', compact('activities'));
    }

    public function previewGeneral()
    {
        $activities = Activity::where('activity_type_id', 1)->get();
        return view('preview_general', compact('activities'));
    }

    function showListActivity()
    {
        $requestListActivity = Activity::with('activityType')->paginate(5);
        $activityTypes = ActivityType::all();

        return view('admin.activity_list', compact('requestListActivity', 'activityTypes'));
    }

    function delete($activity_id)
    {
        DB::table('activities')->where('activity_id', $activity_id)->delete();
        return redirect('/admin/activity_list');
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
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // ตรวจสอบรูปภาพ


            ],
            [
                'activity_name.required' => 'กรุณาป้อนชื่อกิจกรรม',
                'description.required' => 'กรุณาป้อนรายละเอียดกิจกรรม',
                'children_price.required' => 'กรุณาป้อนราคาเด็ก',
                'student_price.required' => 'กรุณาป้อนราคานร/นศ',
                'adult_price.required' => 'กรุณาป้อนราคาผู้ใหญ่',
                'image.image' => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพ',
                'image.mimes' => 'รูปภาพต้องเป็นไฟล์ชนิด jpeg, png, jpg, หรือ gif',

            ]
        );

        $activity = new Activity();
        $activity->activity_name = $request->activity_name;
        $activity->description = $request->description;
        $activity->children_price = $request->children_price;
        $activity->student_price = $request->student_price;
        $activity->adult_price = $request->adult_price;
        $activity->max_capacity = $request->max_capacity;
        $activity->activity_type_id = $request->activity_type_id;

        // จัดการการอัปโหลดรูปภาพ
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $activity->image = $imageName;
        }
        $activity->save();
        return redirect('/admin/activity_list')->with('success', 'เพิ่มกิจกรรมเรียบร้อยแล้ว!');
    }

    public function updateActivity(Request $request)
    {
        // ค้นหากิจกรรมตาม activity_id ที่ส่งมา
        $activity = Activity::find($request->activity_id);
        if (!$activity) {
            return redirect()->back()->with('error', 'ไม่พบกิจกรรมที่ต้องการแก้ไข');
        }

        $activity->activity_name = $request->activity_name;
        $activity->description = $request->description;
        $activity->children_price = $request->children_price;
        $activity->student_price = $request->student_price;
        $activity->adult_price = $request->adult_price;
        $activity->activity_type_id = $request->activity_type_id;

        // จัดการการอัปโหลดรูปภาพ
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $activity->image = $imageName;
        }

        $activity->save();
        return redirect()->back()->with('success', 'แก้ไขกิจกรรมเรียบร้อยแล้ว');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
        ]);

        ActivityType::create([
            'type_name' => $request->type_name,
        ]);

        return redirect()->back()->with('success', 'เพิ่มประเภทกิจกรรมเรียบร้อยแล้ว');
    }

    // public function createBookingForm()
    // {
    //     $activities = Activity::all();

    //     return view('form_bookings', compact('activities'));
    // }

    // public function getActivityPrice($activity_id)
    // {
    //     $activity = Activity::find($activity_id);

    //     return response()->json([
    //         'children_price' => $activity->children_price,
    //         'student_price' => $activity->student_price,
    //         'adult_price' => $activity->adult_price
    //     ]);
    // }

}
