<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    /**
     * Handle user redirection after login based on role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */

    protected function authenticated(Request $request, $user)
    {
        if (!$user->is_approved) {
            Auth::logout();
            return redirect('/login')->with('error', 'บัญชีของคุณยังไม่ได้รับการอนุมัติเข้าใช้งาน');
        }
        // Check if the user has a role assigned
        if (is_null($user->role_id)) {
            Auth::logout();
            return redirect('/login')->with('error', 'บัญชีของคุณไม่มีบทบาทที่กำหนด');
        }

        if ($user->role && $user->role->role_name === 'Super Admin') {
            return redirect('super_admin/all_users');
        } elseif ($user->role && $user->role->role_name === 'Admin') {
            return redirect('admin/dashboard');
        } elseif ($user->role && $user->role->role_name === 'Executive') {
            return redirect('executive/dashboard');
        }
        return redirect('/');
    }

    /**
     * Set the redirect path after logout.
     */
    protected $redirectTo = '/login';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect('/login')
            ->withErrors([
                'email' => '',
                'password' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'
            ])
            ->withInput($request->only('email', 'remember'));
    }
}
