<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class SuperAdminController extends Controller
{
    public function showDashboard()
    {
        return view('superadmin.dashboard');
    }

    public function approveUsers(Request $request, $user_id)
    {
        $user = User::find($user_id);
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
}
