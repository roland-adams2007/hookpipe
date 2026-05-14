<?php

namespace App\Jobs;

use App\Models\WebhookEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessWebhookJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public WebhookEvent $event) {}

    public function handle(): void
    {
        $this->event->markAsProcessing();

        try {
            // Your custom processing logic here
            $this->event->markAsProcessed();
        } catch (\Throwable $th) {
            $this->event->markAsFailed($th->getMessage());
        }
    }
}