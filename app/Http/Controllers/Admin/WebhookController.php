<?php

namespace App\Http\Controllers\Admin;

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
        $this->authorize('viewAny', Webhook::class);

        $webhooks = $this->webhookService->getWebhooksForCompany(
            auth()->user()->company_id,
            $request->only(['is_active', 'per_page'])
        );

        if (view()->exists('admin.webhooks.index')) {
            return view('admin.webhooks.index', compact('webhooks'));
        }

        return response()->json($webhooks);
    }

    public function store(StoreWebhookRequest $request)
    {
        $this->authorize('create', Webhook::class);

        $webhook = $this->webhookService->createWebhook(
            $request->validated(),
            auth()->user()->company_id
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Webhook registered.', 'webhook' => $webhook], 201);
        }

        toastr()->success('Webhook registered successfully.');
        return redirect()->route('admin.webhooks.index');
    }

    public function show(Webhook $webhook)
    {
        $this->authorize('view', $webhook);

        $webhook->load(['logs' => fn($q) => $q->latest()->take(50)]);

        if (request()->wantsJson()) {
            return response()->json($webhook);
        }

        return view('admin.webhooks.show', compact('webhook'));
    }

    public function test(Request $request, Webhook $webhook)
    {
        $this->authorize('update', $webhook);

        $payload = [
            'event' => 'webhook.test',
            'timestamp' => now()->toIso8601String(),
            'message' => 'This is a test webhook payload from SAA platform.',
        ];

        $log = $this->webhookService->dispatchWebhook($webhook, 'webhook.test', $payload);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Test webhook ping dispatched.', 'log' => $log]);
        }

        toastr()->success('Test webhook dispatched with status: ' . ($log->status ?? 'sent'));
        return redirect()->back();
    }

    public function destroy(Webhook $webhook)
    {
        $this->authorize('delete', $webhook);

        $webhook->delete();

        toastr()->success('Webhook deleted.');
        return redirect()->route('admin.webhooks.index');
    }
}