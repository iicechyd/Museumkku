<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\UserLog;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        if ($event->user) {
            $existingLog = UserLog::where('user_id', $event->user->user_id)
                ->whereNull('logout_at')
                ->first();

            if (!$existingLog) {
                UserLog::create([
                    'user_id' => $event->user->user_id,
                    'ip_address' => request()->ip(),
                    'login_at' => now(),
                ]);
            } else {
                Log::warning('Duplicate login detected for user_id: ' . $event->user->user_id);
            }
        } else {
            Log::error('User not found during login event');
        }
    }
}
