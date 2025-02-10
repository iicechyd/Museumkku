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
        if (Auth::user()->role_id === $user->role_id && $user->role_id == 1) {
            return redirect()->route('showAllUsers')->with('error', 'Superadmin ไม่สามารถเปลี่ยนบทบาทตัวเอง');
        }
        $user->is_approved = true;
        $user->role_id = $request->role_id;
        $user->save();
        return redirect()->route('showAllUsers')->with('success', 'บัญชีผู้ใช้งานได้รับการอนุมัติเสร็จสิ้น');
    }

    public function showAllUsers()
    {
        $users = User::with('role')->get();
        $roles = Role::all();
        return view('superadmin.all_users', compact('users', 'roles'));
    }

    public function showUserLogs()
    {
        $logs = UserLog::with('user')->get();
        return view('superadmin.user_logs', compact('logs'));
    }
    public function logUserLogin(Request $request)
    {
        $existingLog = UserLog::where('user_id', Auth::id())
            ->whereNull('logout_at')
            ->first();
        if ($existingLog) {
            return;
        }
        $log = new UserLog();
        $log->user_id = Auth::id();
        $log->ip_address = $request->ip();
        $log->login_at = now();
        $log->save();
    }
}
