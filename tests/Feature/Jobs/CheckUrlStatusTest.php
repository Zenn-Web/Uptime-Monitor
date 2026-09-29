<?php

namespace Tests\Feature\Jobs;

use App\Models\Monitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UrlDownNotification;
use App\Jobs\CheckUrlStatus;
use Tests\TestCase;

class CheckUrlStatusTest extends TestCase
{
    use RefreshDatabase;
    public function test_monitor_marked_down_when_status_mismatch(): void
    {
        Http::fake([
            'https://example.com' => Http::response('', 200),
        ]);

        $monitor = Monitor::factory()->create([
            'url' => 'https://example.com',
            'expected_status' => 400,
        ]);
        $job = new CheckUrlStatus($monitor);
        $job->handle();
        $this->assertDatabaseHas('monitors', [
            'id' => $monitor->id,
            'is_up' => false,
        ]);
        
    }

    public function test_monitor_marked_up_when_status_matches(): void
    {
        Http::fake([
            'https://example.com' => Http::response('', 200),
        ]);
        $monitor = Monitor::factory()->create([
            'url' => 'https://example.com',
            'expected_status' => 200,
        ]);
        $job = new CheckUrlStatus($monitor);
        $job->handle();
        $this->assertDatabaseHas('monitors', [
            'id' => $monitor->id,
            'is_up' => true,
        ]);
    }

    public function test_monitor_marked_down_when_request_fails(): void
    {
        Http::fake([
            'https://example.com' => Http::failedConnection(),
        ]);
        $monitor = Monitor::factory()->create([
            'url' => 'https://example.com',
            'expected_status' => 200,
        ]);
        $job = new CheckUrlStatus($monitor);
        $job->handle();
        $this->assertDatabaseHas('monitors', [
            'id' => $monitor->id,
            'is_up' => false,
        ]);
    }

    public function test_notification_sent_when_monitor_goes_down(): void
    {
        Notification::fake();
        Http::fake([
            'https://example.com' => Http::response('', 500),
        ]);
        $user = User::factory()->create();
        $monitor = Monitor::factory()->create([
            'is_up' => true,
            'url' => 'https://example.com',
            'expected_status' => 200,
        ]);
        $job = new CheckUrlStatus($monitor);
        $job->handle();
        Notification::assertSentTo(
            User::first(),
            UrlDownNotification::class
        );
    }

    public function test_notification_sent_only_once_when_monitor_goes_down(): void
    {
        Notification::fake();
        Http::fake([
            'https://example.com' => Http::response('', 500),
        ]);
        $user = User::factory()->create();
        $monitor = Monitor::factory()->create([
            'is_up' => true,
            'url' => 'https://example.com',
            'expected_status' => 200,
        ]);
        $job = new CheckUrlStatus($monitor);
        $job->handle();
        $monitor->refresh();
        $job = new CheckUrlStatus($monitor);
        $job->handle();
        Notification::assertSentToTimes(
            User::first(),
            UrlDownNotification::class,
            1
        );
    }

    public function test_incident_is_created_when_monitor_goes_down(): void
    {
        Notification::fake();

        Http::fake(['https://example.com' => Http::response('', 500)]);

        User::factory()->create();

        $monitor = Monitor::factory()->create([
            'is_up' => true,
            'url' => 'https://example.com',
            'expected_status' => 200,
        ]);

        (new CheckUrlStatus($monitor))->handle();

        $this->assertDatabaseHas('incidents', 
        ['monitor_id' => $monitor->id, 
        'status' => 'down', 
        ]);
    }

    public function test_incident_is_marked_as_recovered(): void
    {
        Http::fake([
        'https://example.com' => Http::response('', 200),
        ]);

        $monitor = Monitor::factory()->create([
        'is_up' => false,
        'url' => 'https://example.com',
        'expected_status' => 200,
        ]);

        $incident = $monitor->incidents()->create([
        'status' => 'down',
        'detected_at' => now()->subMinutes(10),
        'note' => 'Website tidak merespons',
        ]);

        (new CheckUrlStatus($monitor))->handle();

        $this->assertDatabaseHas('incidents', [
        'id' => $incident->id,
        'status' => 'recovered',
        ]);

        $this->assertNotNull(
        $incident->fresh()->resolved_at
        );
    }
}
