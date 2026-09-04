<?php

namespace Tests\Feature\Automation;

use App\Models\Company;
use App\Models\Workflow;
use App\Services\Automation\WorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowAutomationTest extends TestCase
{
    use RefreshDatabase;

    public function test_workflow_evaluates_conditions_and_executes_actions()
    {
        $company = Company::create(['name' => 'Auto Corp']);

        $service = app(WorkflowService::class);
        $workflow = $service->createWorkflow([
            'name' => 'On Quote Accepted',
            'trigger' => 'quote.accepted',
            'is_active' => true,
            'conditions' => ['status' => 'accepted'],
            'actions' => [
                [
                    'type' => 'log_activity',
                    'configuration' => ['message' => 'Quote accepted workflow triggered.'],
                ],
            ],
        ], $company->id);

        $this->assertDatabaseHas('workflows', ['id' => $workflow->id, 'name' => 'On Quote Accepted']);

        // Execute matching payload
        $log = $service->executeWorkflow($workflow, ['status' => 'accepted', 'amount' => 1200]);
        $this->assertEquals('completed', $log->status);

        // Execute mismatching payload
        $skipLog = $service->executeWorkflow($workflow, ['status' => 'rejected']);
        $this->assertEquals('skipped', $skipLog->status);
    }
}
