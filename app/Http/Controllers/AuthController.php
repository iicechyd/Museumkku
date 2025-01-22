<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\Google;

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
}
