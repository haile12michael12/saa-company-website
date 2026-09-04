<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Services\Integration\WebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Webhook $webhook,
        public string $event,
        public array $payload = []
    ) {}

    public function handle(WebhookService $webhookService): void
    {
        $webhookService->dispatchWebhook($this->webhook, $this->event, $this->payload);
    }
}