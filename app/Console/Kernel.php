<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Clean up old session files daily
        $schedule->command('session:gc')->daily();
        
        // Verify vote integrity weekly
        $schedule->command('votes:verify')->weekly()->sundays()->at('02:00');
        
        // Clean up old logs monthly
        $schedule->call(function () {
            \Illuminate\Support\Facades\DB::table('vote_logs')
                ->where('created_at', '<', now()->subMonths(6))
                ->delete();
        })->monthly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
