<?php

namespace App\Services\Automation;

use App\Jobs\ProcessWorkflow;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowAction;
use App\Models\WorkflowLog;
use App\Services\Communication\EmailService;
use App\Services\Integration\WebhookService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkflowService
{
    public function getWorkflowsForCompany(?int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Workflow::with(['actions', 'logs']);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['trigger'])) {
            $query->where('trigger', $filters['trigger']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function createWorkflow(array $data, ?int $companyId = null): Workflow
    {
        return DB::transaction(function () use ($data, $companyId) {
            $workflow = Workflow::create([
                'company_id' => $companyId,
                'name' => $data['name'],
                'trigger' => $data['trigger'],
                'is_active' => $data['is_active'] ?? true,
                'conditions' => $data['conditions'] ?? null,
            ]);

            if (!empty($data['actions']) && is_array($data['actions'])) {
                foreach ($data['actions'] as $index => $action) {
                    $workflow->actions()->create([
                        'sort_order' => $index,
                        'type' => $action['type'],
                        'configuration' => $action['configuration'] ?? [],
                    ]);
                }
            }

            return $workflow;
        });
    }

    public function triggerEvent(string $triggerName, array $payload, ?int $companyId = null): int
    {
        $query = Workflow::where('trigger', $triggerName)
            ->where('is_active', true);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $workflows = $query->get();

        foreach ($workflows as $workflow) {
            ProcessWorkflow::dispatch($workflow, $payload);
        }

        return $workflows->count();
    }

    public function executeWorkflow(Workflow $workflow, array $payload): WorkflowLog
    {
        $log = WorkflowLog::create([
            'workflow_id' => $workflow->id,
            'status' => 'processing',
            'payload' => $payload,
        ]);

        try {
            // Check conditions
            if (!$this->evaluateConditions($workflow->conditions, $payload)) {
                $log->update([
                    'status' => 'skipped',
                    'message' => 'Workflow conditions not met.',
                    'processed_at' => now(),
                ]);
                return $log;
            }

            $actions = $workflow->actions()->orderBy('sort_order')->get();
            $actionResults = [];

            foreach ($actions as $action) {
                $actionResults[] = $this->executeAction($action, $payload);
            }

            $log->update([
                'status' => 'completed',
                'message' => 'Executed ' . count($actionResults) . ' action(s) successfully.',
                'processed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Workflow #{$workflow->id} execution failed: " . $e->getMessage());
            $log->update([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'processed_at' => now(),
            ]);
        }

        return $log;
    }

    protected function evaluateConditions(?array $conditions, array $payload): bool
    {
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $key => $expectedValue) {
            $actualValue = data_get($payload, $key);
            if ($actualValue != $expectedValue) {
                return false;
            }
        }

        return true;
    }

    protected function executeAction(WorkflowAction $action, array $payload): array
    {
        $config = $action->configuration ?? [];

        switch ($action->type) {
            case 'send_email':
                $email = $config['to'] ?? data_get($payload, 'email');
                $subject = $config['subject'] ?? 'Notification from Workflow';
                $body = $config['body'] ?? 'Your workflow action has executed.';

                if ($email) {
                    app(EmailService::class)->sendEmail($email, $subject, $body);
                }
                return ['type' => 'send_email', 'status' => 'sent', 'to' => $email];

            case 'dispatch_webhook':
                $url = $config['url'] ?? null;
                if ($url) {
                    app(WebhookService::class)->sendRawWebhook($url, $payload, $config['secret'] ?? null);
                }
                return ['type' => 'dispatch_webhook', 'status' => 'dispatched', 'url' => $url];

            case 'log_activity':
            default:
                Log::info("Workflow Action executed: " . json_encode($config));
                return ['type' => 'log_activity', 'status' => 'logged'];
        }
    }
}