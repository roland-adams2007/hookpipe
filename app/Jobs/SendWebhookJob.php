<?php

namespace App\Jobs;

use App\Models\WebhookEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(
        private string $webhookEventId,
        private string $url,
        private array $payload,
        private string $secret
    ) {}

    public function handle(): void
    {
        $webhookEvent = WebhookEvent::find($this->webhookEventId);

        if (!$webhookEvent) {
            return;
        }
        try {
            $webhookEvent->markAsProcessing();
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-Webhook-Secret' => $this->secret,
                ])
                ->post($this->url, $this->payload);
            if ($response->successful()) {
                $webhookEvent->markAsProcessed();
            } else {
                $webhookEvent->markAsFailed(
                    "HTTP {$response->status()}: " . substr($response->body(), 0, 500)
                );
            }
        } catch (\Exception $e) {
            $webhookEvent->markAsFailed($e->getMessage());
            throw $e;
        }
    }


    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10)->toDateTime();
    }

    public function failed(\Throwable $e): void
    {
        $webhookEvent = WebhookEvent::find($this->webhookEventId);
        if ($webhookEvent) {
            $webhookEvent->markAsFailed('Max retries exceeded: ' . $e->getMessage());
        }
    }
}
