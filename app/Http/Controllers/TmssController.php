<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Activity;
use App\Models\Tmss;
use App\Models\Bookings;
use App\Models\closedTmss;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Log;

class TmssController extends Controller
{
    public function showTmss()
    {
        $activities = Activity::with('tmss')->get();

        return view('admin.tmss_list', compact('activities'));
    }

    public function update(Request $request, $id)
    {
        $tmss = Tmss::findOrFail($id);
        $tmss->start_time = $request->input('start_time');
        $tmss->end_time = $request->input('end_time');
        $tmss->save();

        return redirect()->back()->with('success', 'แก้ไขรอบการเข้าชมเรียบร้อยแล้ว');
    }

    public function InsertTmss(Request $request)
    {
        $messages = [
            'end_time.after' => 'เวลาสิ้นสุดต้องช้ากว่าเวลาเริ่มต้น กรุณาเลือกเวลาใหม่อีกครั้ง',
        ];
        $request->validate([
            'activity_id' => 'required|exists:activities,activity_id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|after:start_time',
        ], $messages);

        $tmss = new Tmss();
        $tmss->activity_id = $request->input('activity_id');
        $tmss->start_time = $request->input('start_time');
        $tmss->end_time = $request->input('end_time');
        $tmss->save();

        return redirect()->back()->with('success', 'เพิ่มรอบการเข้าชมเรียบร้อยแล้ว');
    }

        public function delete($id)
    {
        $hasPendingBookings = Bookings::whereHas('tmss', function ($query) use ($id) {
            $query->where('tmss.tmss_id', $id);
        })
        ->whereIn('status', [0, 1])
        ->exists();
    
        if ($hasPendingBookings) {
            return redirect()->back()->with('error', 'ไม่สามารถลบรอบการเข้าชมนี้ได้ เนื่องจากมีการจองรอบการเข้าชมของกิจกรรมนี้ที่รอดำเนินการในระบบ');
        }

        $tmss = Tmss::findOrFail($id);
        $tmss->delete();

        return redirect()->back()->with('success', 'ลบรอบการเข้าชมเรียบร้อยแล้ว');
    }

    public function toggleStatus($id)
    {
        $tmss = Tmss::findOrFail($id);
        $tmss->status = ($tmss->status === 1) ? 0 : 1;
        $tmss->save();

        return response()->json([
            'status' => $tmss->status,
            'message' => 'สถานะของกิจกรรมถูกเปลี่ยนเรียบร้อยแล้ว'
        ]);
    }
    public function showClosedDates()
    {
        $activities = Activity::all();

        $closedDates = ClosedTmss::with(['activity', 'tmss'])
            ->select('closed_tmss_id', 'activity_id', 'tmss_id', 'closed_on', 'comments')
            ->orderBy('closed_on', 'desc')
            ->get();

        return view('admin.manage_closed_dates', compact('activities', 'closedDates'));
    }

    public function saveClosedDates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activity_id' => 'required|exists:activities,activity_id',
            'tmss_id' => 'required',
            'closed_on' => 'required|date_format:d/m/Y',
            'comments' => 'required|string|max:255',
        ], [
            'activity_id.required' => 'กรุณาเลือกประเภทการเข้าชม',
            'tmss_id.required' => 'กรุณาเลือกรอบการเข้าชม',
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
        $tmssId = $request->input('tmss_id');

        $closedOn = DateTime::createFromFormat('d/m/Y', $request->closed_on);
        if (!$closedOn) {
            return back()->with('error', 'รูปแบบวันที่ไม่ถูกต้อง')->withInput();
        }
        $formattedDate = $closedOn->format('Y-m-d');
        $comments = $request->input('comments');

            $existingBookings = Bookings::where('activity_id', $activityId)
            ->where('booking_date', $formattedDate)
            ->when($tmssId !== 'all', function ($query) use ($tmssId) {
                return $query->where('tmss_id', $tmssId);
            })
            ->where('status', 0,1)
            ->exists();

        if ($existingBookings) {
            return redirect()->back()->with('error', 'ไม่สามารถบันทึกข้อมูลการปิดรอบการเข้าชมนี้ได้ เนื่องจากมีการจองรอบการเข้าชมที่เลือกในวันที่เดียวกันที่รอดำเนินการอยู่ในระบบ')->withInput();
        }
        $existingAllClosed = ClosedTmss::where('activity_id', $activityId)
        ->whereNull('tmss_id')
        ->where('closed_on', $formattedDate)
        ->exists();
        
        if ($existingAllClosed && $tmssId !== 'all') {
            return redirect()->back()->with('error', 'ไม่สามารถบันทึกข้อมูลได้ เนื่องจากมีการปิดทุกรอบแล้ว')->withInput();
        }

        $existingClosedTmss = ClosedTmss::where('activity_id', $activityId)
        ->where('closed_on', $formattedDate)
        ->when($tmssId !== 'all', function ($query) use ($tmssId) {
            return $query->where('tmss_id', $tmssId);
        })
        ->exists();

        if ($existingClosedTmss) {
            return redirect()->back()->with('error', 'ไม่สามารถบันทึกข้อมูลซ้ำได้')->withInput();
        }

        ClosedTmss::create([
            'activity_id' => $activityId,
            'tmss_id' => $tmssId === 'all' ? null : $tmssId,
            'closed_on' => $formattedDate,
            'comments' => $comments,
        ]);

        return redirect()->back()->with('success', 'บันทึกข้อมูลการปิดรอบการเข้าชมเรียบร้อยแล้ว');
    }
    public function deleteClosedDate($id)
    {
        try {
            $closedTmss = ClosedTmss::findOrFail($id);
            $closedTmss->delete();

            return redirect()->back()->with('success', 'ยกเลิกวันที่ปิดรอบการเข้าชมสำเร็จ');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการลบวันที่ปิดรอบการเข้าชม');
        }
    }

    public function getTmssByActivity(Request $request)
    {
        $tmss = Tmss::where('activity_id', $request->activity_id)
            ->get()
            ->map(function ($tmss) {
                $tmss->start_time = Carbon::parse($tmss->start_time)->format('H:i') . ' น.';
                $tmss->end_time = Carbon::parse($tmss->end_time)->format('H:i') . ' น.';
                return $tmss;
            });
        return response()->json($tmss);
    }
    public function getAvailableTmss($activity_id, $date)
    {
        Log::debug("Activity ID: $activity_id, Date: $date");

        $closedTmssIds = ClosedTmss::where('closed_on', $date)
            ->where('activity_id', $activity_id)
            ->pluck('tmss_id');

        Log::debug("ClosedTmss IDs: ", $closedTmssIds->toArray());

        $availableTmss = Tmss::where('activity_id', $activity_id)
            ->where('status', 1)    
            ->whereNotIn('tmss_id', $closedTmssIds)
            ->get();
        Log::debug("Available TMSS: ", $availableTmss->toArray());

        $availableTmssWithCapacity = $availableTmss->map(function ($tmss) use ($activity_id, $date) {
            $totalApproved = Bookings::where('booking_date', $date)
                ->where('activity_id', $activity_id)
                ->where('tmss_id', $tmss->tmss_id)
                ->whereIn('status', [0, 1])
                ->sum(DB::raw('children_qty + students_qty + adults_qty + kid_qty + disabled_qty + elderly_qty + monk_qty'));

            if ($tmss->activity->max_capacity !== null) {
                $remainingCapacity = $tmss->activity->max_capacity - $totalApproved;
            } else {
                $remainingCapacity = 'ไม่จำกัดจำนวนคน';
            }

            $tmss->remaining_capacity = $remainingCapacity;
            return $tmss;
        });
        return response()->json($availableTmssWithCapacity);
    }
}
