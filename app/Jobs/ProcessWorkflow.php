<?php

namespace App\Jobs;

use App\Models\Workflow;
use App\Services\Automation\WorkflowService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWorkflow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Workflow $workflow,
        public array $payload = []
    ) {}

    public function handle(WorkflowService $service): void
    {
        $service->executeWorkflow($this->workflow, $this->payload);
    }
}