<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class CheckUrlStatus implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Monitor $monitor,

    ){}

    public function handle() :void
    {
        $StatusSebelumnya = $this->monitor->is_up;

        try {
            $response = Http::get($this->monitor->url);
            $this->monitor->is_up = $response->status() === $this->monitor->expected_status;
        } catch (\Exception $e) {
            $this->monitor->is_up = false;
        }
        
        $this->monitor->last_checked_at = now();
        $this->monitor->save(); 
        
        if ($StatusSebelumnya && !$this->monitor->is_up) {
            User::first()->notify(new \App\Notifications\UrlDownNotification($this->monitor));
        }
    }
}
