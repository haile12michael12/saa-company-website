<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkflowRequest;
use App\Services\Automation\WorkflowService;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function __construct(protected WorkflowService $workflowService) {}

    public function index(Request $request)
    {
        return app(WorkflowController::class)->index($request);
    }

    public function store(StoreWorkflowRequest $request)
    {
        return app(WorkflowController::class)->store($request);
    }
}