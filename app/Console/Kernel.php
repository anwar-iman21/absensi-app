<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Hitung ulang payroll setiap hari jam 00:05
        // $schedule->call(function () {
        //     \App\Models\Payroll::where('status','proses')->each(fn($p) => $p->hitungGaji());
        // })->dailyAt('00:05');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
