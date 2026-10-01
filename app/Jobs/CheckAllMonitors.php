<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Monitor;


class CheckAllMonitors implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        
    }

    public function handle(): void
    {
        Monitor::all()->each(function (Monitor $monitor) {
            CheckUrlStatus::dispatch($monitor);
        });
        
    }
}
