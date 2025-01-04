<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\UserLog;

class SuperAdminController extends Controller
{
    public function showDashboard()
    {
        return view('superadmin.dashboard');
    }

    public function approveUsers(Request $request, $user_id)
    {
        $user = User::find($user_id);
         // ตรวจสอบว่าเป็น superadmin หรือไม่
         if (Auth::user()->role_id === $user->role_id && $user->role_id == 1) {  // สมมติว่า role_id 1 คือ superadmin
            return redirect()->route('showAllUsers')->with('error', 'Superadmin cannot change their own role.');
        }

        $user->is_approved = true;
        $user->role_id = $request->role_id;
        $user->save();

        return redirect()->route('showAllUsers')->with('success', 'User approved successfully.');
    }

    public function showAllUsers()
    {
        $users = User::with('role')->get();
        $roles = Role::all();

        return view('superadmin.all_users', compact('users', 'roles'));
    }

    public function showUserLogs()
    {
        $logs = UserLog::with('user')->get(); // โหลดความสัมพันธ์ 'user' พร้อมกับบันทึก
    
        return view('superadmin.user_logs', compact('logs'));
    }
    public function logUserLogin(Request $request)
{
    // ตรวจสอบว่า user_id นี้ยังไม่ได้ logout (logout_at เป็น null)
    $existingLog = UserLog::where('user_id', Auth::id())
                          ->whereNull('logout_at') // ตรวจสอบว่า logout_at เป็น null
                          ->first();

    // หากพบว่า user นี้ล็อกอินอยู่แล้ว ก็ไม่ทำการบันทึกใหม่
    if ($existingLog) {
        return;
    }

    // บันทึกข้อมูลใหม่หากไม่พบว่าผู้ใช้ล็อกอินอยู่แล้ว
    $log = new UserLog();
    $log->user_id = Auth::id();
    $log->ip_address = $request->ip();
    $log->login_at = now();
    $log->save();
}

    
}
