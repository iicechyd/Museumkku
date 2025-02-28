<?php

use Illuminate\Support\Facades\Artisan;
use App\Models\Verification;
use Carbon\Carbon;

Artisan::command('clean:expired-verifications', function () {
    $deleted = Verification::where('verified', false)
        ->where('expires_at', '<', Carbon::now())
        ->delete();

    $this->info("Deleted $deleted expired verification records.");
})->purpose('Delete expired verification records')->daily();