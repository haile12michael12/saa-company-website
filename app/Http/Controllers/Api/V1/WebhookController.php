<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWebhookRequest;
use App\Models\Webhook;
use App\Services\Integration\WebhookService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(protected WebhookService $webhookService) {}

    public function index(Request $request)
    {
        $webhooks = $this->webhookService->getWebhooksForCompany(
            $request->user()?->company_id,
            $request->only(['is_active', 'per_page'])
        );

        return response()->json($webhooks);
    }

    public function store(StoreWebhookRequest $request)
    {
        $webhook = $this->webhookService->createWebhook(
            $request->validated(),
            $request->user()?->company_id
        );

        return response()->json($webhook, 201);
    }

    public function show(Webhook $webhook)
    {
        return response()->json($webhook->load(['logs']));
    }

    public function destroy(Webhook $webhook)
    {
        $webhook->delete();
        return response()->json(['message' => 'Webhook deleted.']);
    }
}