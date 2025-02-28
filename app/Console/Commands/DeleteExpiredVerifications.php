<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Verification;
use Carbon\Carbon;

class DeleteExpiredVerifications extends Command
{
    protected $signature = 'verifications:delete-expired';
    protected $description = 'Delete expired verifications that are not confirmed';

    public function handle()
    {
        $expiredVerifications = Verification::where('verified', false)
            ->where('expires_at', '<', Carbon::now())
            ->delete();

        $this->info('Expired verifications deleted.');
    }
}
