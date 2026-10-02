<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckAllMonitors implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function handle(): void
    {
        Monitor::all()->each(function (Monitor $monitor) {
            CheckUrlStatus::dispatch($monitor);
        });

    }
}
