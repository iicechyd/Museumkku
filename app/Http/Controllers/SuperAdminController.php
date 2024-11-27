<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class SuperAdminController extends Controller
{
    public function showPendingUsers()
    {
        $users = User::where('is_approved', false)->get();
        $roles = Role::all();

        return view('superadmin.pending_users', compact('users', 'roles'));
    }

    public function approveUsers(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_approved = true;
        $user->role_id = $request->role_id;
        $user->save();

        return redirect()->back()->with('success', 'User approved successfully.');
    }

    public function index(){
        return view('superadmin.pending_users');
    }
}
