<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Verification;

class AuthController extends Controller
{
    public function showGuestVerify()
    {
        return view('guest_verify');
    }
    public function waitingForVerification()
    {
        return view('waiting_for_verification');
    }

    public function sendVerificationLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->input('email');
        $token = Str::random(40);

        Verification::updateOrCreate(
            ['email' => $email],
            ['token' => $token, 'verified' => false]
        );

        $verificationLink = route('verifyLink', ['token' => $token]);
        session(['verification_email' => $email]);

        Mail::send('emails.verify_link', ['url' => $verificationLink], function ($message) use ($email) {
            $message->to($email)->subject('ลิงก์ยืนยันตัวตน');
        });

        return redirect()->route('waiting_for_verification');
    }

    public function verifyLink($token)
    {
        $verification = Verification::where('token', $token)->first();

        if (!$verification) {
            return redirect('/')->with('error', 'ลิงก์ยืนยันไม่ถูกต้องหรือหมดอายุ');
        }

        $verification->update(['verified' => true]);

        session(['verification_email' => $verification->email]);

        session(['redirect_url' => route('form_bookings.activity', ['activity_id' => 1])]);

    return view('verified');
    }

    public function checkVerification($email)
    {
        $verification = Verification::where('email', $email)->first();

        return response()->json(['verified' => $verification ? $verification->verified : false]);
    }

    public function clearEmailSession()
    {
        session()->forget('verification_email');
        return redirect()->route('guest.verify')->with('success', 'เคลียร์อีเมลเรียบร้อยแล้ว');
    }
}
