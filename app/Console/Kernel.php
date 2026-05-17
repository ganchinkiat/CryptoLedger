<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Define scheduled tasks here.
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
