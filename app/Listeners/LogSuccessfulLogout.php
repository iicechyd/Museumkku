<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Logout;
use App\Models\UserLog;
class LogSuccessfulLogout
{
    public function handle(Logout $event)
    {
        $log = UserLog::where('user_id', $event->user->user_id)
            ->whereNull('logout_at')
            ->latest()
            ->first();

        if ($log) {
            $log->update(['logout_at' => now()]);
        }
    }
}
