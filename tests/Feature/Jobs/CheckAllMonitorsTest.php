<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CheckAllMonitors;
use App\Jobs\CheckUrlStatus;
use App\Models\Monitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;


class CheckAllMonitorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispatches_a_check_job_for_each_monitor(): void
    {
        Queue::fake();

        Monitor::factory()->count(3)->create();

        (new CheckAllMonitors())->handle();

        Queue::assertPushed(CheckUrlStatus::class, 3);
        
    }
}
