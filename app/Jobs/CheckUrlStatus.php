<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class CheckUrlStatus implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Monitor $monitor,

    ){}

    public function handle() :void
    {
        $StatusSebelumnya = $this->monitor->is_up;
        $StatusSekarang = false;
        $Catatan = null;

        try {
            $response = Http::timeout(10)->get($this->monitor->url);
            $StatusSekarang = $response->status() === $this->monitor->expected_status;
           if (! $StatusSekarang) {
            $Catatan = "HTTP status diterima: {$response->status()}";
           }
        } catch (ConnectionException $exception) {
            $StatusSekarang = false;
            $Catatan = substr($exception->getMessage(), 0, 255);
        }
        
        $this->monitor->is_up = $StatusSekarang;
        $this->monitor->last_checked_at = Carbon::now();
        $this->monitor->save();
        
        if ($StatusSebelumnya && !$StatusSekarang) {
           $this->monitor->incidents()->create(['status' => 'down', 
           'detected_at' => now(), 
           'note' => $Catatan,
           ]);

           User::first()?->notify(
                new \App\Notifications\UrlDownNotification($this->monitor)
           );
        }

        if (! $StatusSebelumnya && $StatusSekarang) {
            $incident = $this->monitor->incidents()
            ->where('status', 'down')
            ->whereNull('resolved_at')
            ->latest('detected_at')
            ->first();

            $incident?->update([
                'status' => 'recovered',
                'resolved_at' => now(),
            ]);
        }
    }
}
