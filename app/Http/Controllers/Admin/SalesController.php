<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Proposal;
use App\Models\Quote;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Quote::class);

        $companyId = auth()->user()->company_id;

        $quotesQuery = Quote::query();
        if ($companyId) {
            $quotesQuery->where('company_id', $companyId);
        }

        $allQuotes = (clone $quotesQuery)->get();

        $totalQuotes = $allQuotes->count();
        $pipelineValue = $allQuotes->sum('total');

        $acceptedQuotes = $allQuotes->filter(fn ($q) => $q->status === 'accepted');
        $acceptedCount = $acceptedQuotes->count();
        $acceptedValue = $acceptedQuotes->sum('total');

        $pendingApprovalCount = $allQuotes->filter(fn ($q) => $q->status === 'pending_approval')->count();
        $approvedCount = $allQuotes->filter(fn ($q) => $q->status === 'approved')->count();
        $sentCount = $allQuotes->filter(fn ($q) => $q->status === 'sent')->count();
        $draftCount = $allQuotes->filter(fn ($q) => $q->status === 'draft')->count();
        $rejectedCount = $allQuotes->filter(fn ($q) => $q->status === 'rejected')->count();
        $expiredCount = $allQuotes->filter(fn ($q) => $q->isExpired())->count();

        $conversionRate = $totalQuotes > 0 ? round(($acceptedCount / $totalQuotes) * 100, 1) : 0;

        $recentQuotes = (clone $quotesQuery)
            ->with(['items', 'customer', 'lead'])
            ->latest()
            ->take(8)
            ->get();

        $recentProposals = Proposal::with(['quote', 'customer', 'lead'])
            ->latest()
            ->take(5)
            ->get();

        $totalCustomers = Customer::count();
        $totalLeads = Lead::count();

        return view('admin.sales.index', compact(
            'totalQuotes',
            'pipelineValue',
            'acceptedCount',
            'acceptedValue',
            'pendingApprovalCount',
            'approvedCount',
            'sentCount',
            'draftCount',
            'rejectedCount',
            'expiredCount',
            'conversionRate',
            'recentQuotes',
            'recentProposals',
            'totalCustomers',
            'totalLeads'
        ));
    }
}

