<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Google;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new Google([
            'clientId'     => env('GOOGLE_CLIENT_ID'),
            'clientSecret' => env('GOOGLE_CLIENT_SECRET'),
            'redirectUri'  => env('GOOGLE_REDIRECT_URI'),
            'scopes'       => ['email', 'profile'],
        ]);
    }

    public function redirectToGoogle()
    {
        $authorizationUrl = $this->provider->getAuthorizationUrl();
        session(['oauth2state' => $this->provider->getState()]);

        return redirect($authorizationUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        if ($request->input('state') !== session('oauth2state')) {
            session()->forget('oauth2state');
            return redirect('/')->with('error', 'Invalid state.');
        }

        $token = $this->provider->getAccessToken('authorization_code', [
            'code' => $request->input('code'),
        ]);

        $user = $this->provider->getResourceOwner($token);
        $email = $user->toArray()['email'] ?? null;

        if ($email) {
            session(['email' => $email]);
            $redirectUrl = session('redirect_url', route('form_bookings.activity', ['activity_id' => 1]));
            session()->forget('redirect_url');
            return redirect($redirectUrl);
        } else {
            return redirect('/')->with('error', 'Failed to retrieve email.');
        }
    }
    public function showGuestVerify()
    {
        return view('guest_verify');
    }
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $otp = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);

        session(['otp' => $otp, 'email' => $email]);

        Mail::send('emails.sendOTP', ['otp' => $otp], function ($message) use ($email) {
            $message->to($email)
                ->subject('รหัส OTP สำหรับการยืนยันตัวตน');
        });

        return redirect()->route('verifyOtp');
    }

    public function showOtpForm()
    {
        return view('verify_otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|array|min:5|max:5',
            'otp.*' => 'required|numeric',
        ]);

        $enteredOtp = implode('', $request->input('otp'));
        $storedOtp = session('otp');
        $email = session('email');
        $redirectUrl = session('redirect_url');

        if ($enteredOtp == $storedOtp) {
            session()->forget(['otp', 'redirect_url']);
            return redirect($redirectUrl ?? route('form_bookings.activity', ['activity_id' => 1]));
        } else {
            return redirect()->back()
                ->with('error', 'รหัส OTP ไม่ถูกต้อง กรุณาลองอีกครั้ง');
        }
    }
    public function clearEmailSession()
{
    session()->forget('email');
    return redirect()->route('guest.verify')->with('success', 'เคลียร์อีเมลเรียบร้อยแล้ว');
}

}
