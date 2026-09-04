<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use App\Services\Security\SecurityLogService;
use Illuminate\Http\Request;

class SecurityLogController extends Controller
{
    public function __construct(protected SecurityLogService $securityLogService) {}

    public function index(Request $request)
    {
        $logs = $this->securityLogService->getLogsForCompany(
            auth()->user()->company_id,
            $request->only(['severity', 'event_type', 'per_page'])
        );

        if ($request->wantsJson()) {
            return response()->json($logs);
        }

        if (view()->exists('admin.security-logs.index')) {
            return view('admin.security-logs.index', compact('logs'));
        }

        return response()->json($logs);
    }

    public function show(SecurityLog $securityLog)
    {
        $securityLog->load('user');

        if (request()->wantsJson()) {
            return response()->json($securityLog);
        }

        return view('admin.security-logs.show', ['log' => $securityLog]);
    }
}