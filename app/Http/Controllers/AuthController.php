<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Verification;
use App\Models\Visitors;

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
        $expiresAt = now()->addMinutes(30);

        Verification::updateOrCreate(
        ['email' => $email],
            ['token' => $token, 'verified' => false, 'expires_at' => $expiresAt]
        );

        $verificationLink = route('verifyLink', ['token' => $token]);
        session(['verification_email' => $email]);

        Mail::send('emails.verify_link', ['url' => $verificationLink, 'email' => $email], function ($message) use ($email) {
            $message->to($email)->subject('ลิงก์ยืนยันตัวตน');
        });

        return redirect()->route('waiting_for_verification');
    }

    public function verifyLink($token)
    {
        $verification = Verification::where('token', $token)->first();

        if (!$verification) {
            abort(404, 'ลิงก์ยืนยันไม่ถูกต้อง');
        }

        if ($verification->expires_at && $verification->expires_at < now()) {
            abort(419, 'ลิงก์ยืนยันหมดอายุ');
        }

        $verification->update(['verified' => true]);

        $visitor = Visitors::where('visitorEmail', $verification->email)
            ->with('institute')
            ->first();

        if ($visitor) {
            session(['visitor_data' => [
                'visitorName' => $visitor->visitorName ?? '',
                'tel' => $visitor->tel ?? '',
                'instituteName' => $visitor->institute->instituteName ?? '',
                'instituteAddress' => $visitor->institute->instituteAddress ?? '',
                'province' => $visitor->institute->province ?? '',
                'district' => $visitor->institute->district ?? '',
                'subdistrict' => $visitor->institute->subdistrict ?? '',
                'zipcode' => $visitor->institute->zipcode ?? '',
            ]]);
        }
        session(['verification_email' => $verification->email]);
        $redirectUrl = session('redirect_url', route('form_bookings.activity', ['activity_id' => 1]));
        session(['redirect_url' => $redirectUrl]);

        return view('emails.verified');
    }

    public function checkVerification($email)
    {
        $verification = Verification::where('email', $email)->first();

        return response()->json(['verified' => $verification ? $verification->verified : false]);
    }
}
