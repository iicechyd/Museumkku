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
        // session(['is_admin' => $user->role && $user->role->role_name === 'Admin']);

        if ($user->role && $user->role->role_name === 'Super Admin') {
            return redirect('super_admin/dashboard');
        } elseif ($user->role && $user->role->role_name === 'Admin') {
            return redirect('admin/dashboard');
        } elseif ($user->role && $user->role->role_name === 'Executive') {
            return redirect('dashboard');
        }

        // Default redirection if no specific role is found
        return redirect('/');
    }

    /**
     * Set the redirect path after logout.
     */
    protected $redirectTo = '/login';

    /**
     * Constructor.
     */
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
        return redirect()->back()
            ->withErrors(['email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'])
            ->withInput($request->only('email', 'remember'));
    }
}
