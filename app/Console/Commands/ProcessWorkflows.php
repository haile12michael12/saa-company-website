<?php

namespace App\Console\Commands;

use App\Models\Workflow;
use App\Services\Automation\WorkflowService;
use Illuminate\Console\Command;

class ProcessWorkflows extends Command
{
    protected $signature = 'workflows:process {--company= : Optional company ID}';

    protected $description = 'Process scheduled and active workflows';

    public function handle(WorkflowService $workflowService): int
    {
        $companyId = $this->option('company');
        $workflows = Workflow::where('is_active', true)
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->get();

        $this->info("Processing " . $workflows->count() . " active workflow(s)...");

        foreach ($workflows as $workflow) {
            $workflowService->executeWorkflow($workflow, ['source' => 'cron_scheduler', 'executed_at' => now()->toIso8601String()]);
        }

        $this->info('Workflows processed successfully.');

        return self::SUCCESS;
    }
}