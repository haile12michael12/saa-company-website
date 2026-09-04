<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\Security\AuditLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(protected AuditLogService $auditLogService) {}

    public function index(Request $request)
    {
        $logs = $this->auditLogService->getLogsForCompany(
            auth()->user()->company_id,
            $request->only(['action', 'auditable_type', 'user_id', 'per_page'])
        );

        if ($request->wantsJson()) {
            return response()->json($logs);
        }

        if (view()->exists('admin.activity-logs.index')) {
            return view('admin.activity-logs.index', compact('logs'));
        }

        return response()->json($logs);
    }

    public function show(AuditLog $activityLog)
    {
        $activityLog->load('user');

        if (request()->wantsJson()) {
            return response()->json($activityLog);
        }

        return view('admin.activity-logs.show', ['log' => $activityLog]);
    }
}