<?php

namespace App\Services\Integration;

use App\Jobs\ProcessWebhook;
use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    public function getWebhooksForCompany(?int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Webhook::withCount(['logs']);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function createWebhook(array $data, ?int $companyId = null): Webhook
    {
        return Webhook::create([
            'company_id' => $companyId,
            'name' => $data['name'],
            'url' => $data['url'],
            'secret' => $data['secret'] ?? null,
            'event' => $data['event'] ?? '*',
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function updateWebhook(Webhook $webhook, array $data): Webhook
    {
        $webhook->update($data);
        return $webhook;
    }

    public function dispatchToAll(string $event, array $payload, ?int $companyId = null): int
    {
        $query = Webhook::where('is_active', true)
            ->where(function ($q) use ($event) {
                $q->where('event', $event)
                  ->orWhere('event', '*')
                  ->orWhereNull('event');
            });

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $webhooks = $query->get();

        foreach ($webhooks as $webhook) {
            ProcessWebhook::dispatch($webhook, $event, $payload);
        }

        return $webhooks->count();
    }

    public function dispatchWebhook(Webhook $webhook, string $event, array $payload): WebhookLog
    {
        $timestamp = now()->timestamp;
        $jsonPayload = json_encode($payload);
        $signature = hash_hmac('sha256', $timestamp . '.' . $jsonPayload, $webhook->secret ?? 'default_secret');

        $log = WebhookLog::create([
            'webhook_id' => $webhook->id,
            'event' => $event,
            'status' => 'pending',
            'attempted_at' => now(),
        ]);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Webhook-Event' => $event,
                    'X-Webhook-Timestamp' => (string)$timestamp,
                    'X-Webhook-Signature' => $signature,
                ])
                ->post($webhook->url, $payload);

            $log->update([
                'response_status' => $response->status(),
                'response_body' => substr($response->body(), 0, 1000),
                'status' => $response->successful() ? 'delivered' : 'failed',
            ]);
        } catch (\Throwable $e) {
            Log::warning("Webhook dispatch failed to {$webhook->url}: " . $e->getMessage());
            $log->update([
                'response_status' => 500,
                'response_body' => $e->getMessage(),
                'status' => 'failed',
            ]);
        }

        return $log;
    }

    public function sendRawWebhook(string $url, array $payload, ?string $secret = null): bool
    {
        $timestamp = now()->timestamp;
        $signature = hash_hmac('sha256', $timestamp . '.' . json_encode($payload), $secret ?? 'raw_secret');

        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Webhook-Timestamp' => (string)$timestamp,
                    'X-Webhook-Signature' => $signature,
                ])
                ->post($url, $payload);

            return $response->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
