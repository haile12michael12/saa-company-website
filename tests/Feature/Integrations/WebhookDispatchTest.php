<?php

namespace Tests\Feature\Integrations;

use App\Models\Company;
use App\Models\User;
use App\Models\Webhook;
use App\Services\Integration\WebhookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebhookDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_dispatch_signed_webhook()
    {
        Http::fake([
            'https://webhook.site/receiver' => Http::response(['received' => true], 200),
        ]);

        $company = Company::create(['name' => 'Integrations Co']);
        $webhook = Webhook::create([
            'company_id' => $company->id,
            'name' => 'Zapier Sync',
            'url' => 'https://webhook.site/receiver',
            'secret' => 'super_secret_key_123',
            'event' => 'lead.created',
        ]);

        $service = app(WebhookService::class);
        $log = $service->dispatchWebhook($webhook, 'lead.created', ['lead_id' => 45, 'name' => 'New Customer']);

        $this->assertEquals('delivered', $log->status);
        $this->assertEquals(200, $log->response_status);
        $this->assertDatabaseHas('webhook_logs', [
            'webhook_id' => $webhook->id,
            'event' => 'lead.created',
            'status' => 'delivered',
        ]);

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-Webhook-Signature') &&
                   $request->hasHeader('X-Webhook-Timestamp') &&
                   $request['lead_id'] === 45;
        });
    }
}
