<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkflowRequest;
use App\Models\Workflow;
use App\Services\Automation\WorkflowService;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function __construct(protected WorkflowService $workflowService) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Workflow::class);

        $workflows = $this->workflowService->getWorkflowsForCompany(
            auth()->user()->company_id,
            $request->only(['is_active', 'trigger', 'per_page'])
        );

        if (view()->exists('admin.automation.index')) {
            return view('admin.automation.index', compact('workflows'));
        }

        return response()->json($workflows);
    }

    public function store(StoreWorkflowRequest $request)
    {
        $this->authorize('create', Workflow::class);

        $workflow = $this->workflowService->createWorkflow(
            $request->validated(),
            auth()->user()->company_id
        );

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Workflow rule created.', 'workflow' => $workflow], 201);
        }

        toastr()->success('Workflow created successfully.');
        return redirect()->route('admin.workflows.index');
    }

    public function show(Workflow $workflow)
    {
        $this->authorize('view', $workflow);

        $workflow->load(['actions', 'logs']);

        if (request()->wantsJson()) {
            return response()->json($workflow);
        }

        return view('admin.automation.show', compact('workflow'));
    }

    public function trigger(Request $request, Workflow $workflow)
    {
        $this->authorize('update', $workflow);

        $log = $this->workflowService->executeWorkflow($workflow, $request->get('payload', ['manual_trigger' => true]));

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Workflow manually executed.', 'log' => $log]);
        }

        toastr()->success('Workflow triggered manually.');
        return redirect()->back();
    }

    public function destroy(Workflow $workflow)
    {
        $this->authorize('delete', $workflow);

        $workflow->delete();

        toastr()->success('Workflow rule removed.');
        return redirect()->route('admin.workflows.index');
    }
}
