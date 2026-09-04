@extends('admin.layouts.layout')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Sales Overview</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Sales</div>
        </div>
    </div>

    <div class="section-body">
        <!-- Quick Action Bar -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 class="section-title mt-0">Sales & Pipeline Performance</h2>
                    <p class="section-lead">Manage quotations, track approval flows, deliver proposals, and convert deals into active projects.</p>
                </div>
                <div class="mb-3">
                    <a href="{{ route('admin.quotes.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-plus mr-1"></i> Create Quote
                    </a>
                    <a href="{{ route('admin.quotes.index') }}" class="btn btn-outline-primary btn-lg shadow-sm ml-2">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> All Quotes
                    </a>
                    <a href="{{ route('admin.proposals.index') }}" class="btn btn-outline-info btn-lg shadow-sm ml-2">
                        <i class="fas fa-scroll mr-1"></i> Proposals
                    </a>
                </div>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="row">
            <!-- Pipeline Value -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1 shadow-sm">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Pipeline Value</h4>
                        </div>
                        <div class="card-body">
                            ${{ number_format($pipelineValue, 2) }}
                        </div>
                        <div class="text-muted small px-3 pb-2">{{ $totalQuotes }} total quotations</div>
                    </div>
                </div>
            </div>

            <!-- Won / Accepted Deals -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1 shadow-sm">
                    <div class="card-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Won Revenue</h4>
                        </div>
                        <div class="card-body">
                            ${{ number_format($acceptedValue, 2) }}
                        </div>
                        <div class="text-muted small px-3 pb-2">{{ $acceptedCount }} deals closed / accepted</div>
                    </div>
                </div>
            </div>

            <!-- Pending Approval -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1 shadow-sm">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Pending Approval</h4>
                        </div>
                        <div class="card-body">
                            {{ $pendingApprovalCount }}
                        </div>
                        <div class="text-muted small px-3 pb-2">Requires managerial sign-off</div>
                    </div>
                </div>
            </div>

            <!-- Conversion Rate -->
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1 shadow-sm">
                    <div class="card-icon bg-info">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Conversion Rate</h4>
                        </div>
                        <div class="card-body">
                            {{ $conversionRate }}%
                        </div>
                        <div class="text-muted small px-3 pb-2">Quote-to-win ratio</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Breakdown Bar -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <span class="font-weight-bold text-dark mb-2 mb-md-0"><i class="fas fa-filter mr-1 text-primary"></i> Pipeline Status:</span>
                            <div class="d-flex flex-wrap">
                                <a href="{{ route('admin.quotes.index', ['status' => 'draft']) }}" class="badge badge-secondary mr-2 mb-1 p-2">
                                    Draft: {{ $draftCount }}
                                </a>
                                <a href="{{ route('admin.quotes.index', ['status' => 'pending_approval']) }}" class="badge badge-warning mr-2 mb-1 p-2">
                                    Pending Approval: {{ $pendingApprovalCount }}
                                </a>
                                <a href="{{ route('admin.quotes.index', ['status' => 'approved']) }}" class="badge badge-info mr-2 mb-1 p-2">
                                    Approved: {{ $approvedCount }}
                                </a>
                                <a href="{{ route('admin.quotes.index', ['status' => 'sent']) }}" class="badge badge-primary mr-2 mb-1 p-2">
                                    Sent: {{ $sentCount }}
                                </a>
                                <a href="{{ route('admin.quotes.index', ['status' => 'accepted']) }}" class="badge badge-success mr-2 mb-1 p-2">
                                    Accepted: {{ $acceptedCount }}
                                </a>
                                <a href="{{ route('admin.quotes.index', ['status' => 'rejected']) }}" class="badge badge-danger mr-2 mb-1 p-2">
                                    Rejected: {{ $rejectedCount }}
                                </a>
                                <a href="{{ route('admin.quotes.index', ['status' => 'expired']) }}" class="badge badge-dark mb-1 p-2">
                                    Expired: {{ $expiredCount }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Quotations & Activity -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-file-invoice mr-2 text-primary"></i> Recent Quotations</h4>
                        <a href="{{ route('admin.quotes.index') }}" class="btn btn-sm btn-outline-primary">View All Quotes</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Quote #</th>
                                        <th>Recipient</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Valid Until</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentQuotes as $q)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.quotes.show', $q) }}" class="font-weight-bold">
                                                {{ $q->number }}
                                            </a>
                                            @if($q->title)
                                                <div class="text-muted small">{{ Str::limit($q->title, 25) }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $q->recipient_name }}</div>
                                            @if($q->recipient_company && $q->recipient_company !== 'Client Organization')
                                                <div class="text-muted small">{{ $q->recipient_company }}</div>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold text-dark">
                                            {{ $q->currency }} {{ number_format($q->total, 2) }}
                                        </td>
                                        <td>
                                            @php $status = $q->effective_status; @endphp
                                            @if($status === 'accepted')
                                                <span class="badge badge-success">Accepted</span>
                                            @elseif($status === 'pending_approval')
                                                <span class="badge badge-warning">Pending Approval</span>
                                            @elseif($status === 'approved')
                                                <span class="badge badge-info">Approved</span>
                                            @elseif($status === 'sent')
                                                <span class="badge badge-primary">Sent</span>
                                            @elseif($status === 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @elseif($status === 'expired')
                                                <span class="badge badge-dark">Expired</span>
                                            @else
                                                <span class="badge badge-secondary">Draft</span>
                                            @endif
                                        </td>
                                        <td class="small">
                                            {{ $q->valid_until ? $q->valid_until->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.quotes.show', $q) }}" class="btn btn-sm btn-info" title="Manage Quote">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.quotes.pdf', $q) }}" class="btn btn-sm btn-light" title="Download PDF">
                                                <i class="fas fa-file-pdf text-danger"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No quotations found. <a href="{{ route('admin.quotes.create') }}">Create the first quote!</a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Widgets -->
            <div class="col-lg-4">
                <!-- Recent Proposals -->
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-scroll mr-2 text-info"></i> Recent Proposals</h4>
                        <a href="{{ route('admin.proposals.index') }}" class="btn btn-sm btn-outline-info">Manage</a>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($recentProposals as $p)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="font-weight-bold">{{ Str::limit($p->title, 26) }}</div>
                                    <div class="text-muted small">
                                        For Quote: <strong>{{ $p->quote->number ?? 'N/A' }}</strong>
                                    </div>
                                </div>
                                <span class="badge badge-light border">{{ ucfirst($p->status) }}</span>
                            </li>
                            @empty
                            <li class="list-group-item text-center text-muted py-3">
                                No proposals created yet.
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4><i class="fas fa-address-book mr-2 text-primary"></i> CRM Database</h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 border-right">
                                <h3 class="font-weight-bold text-primary mb-0">{{ $totalCustomers }}</h3>
                                <div class="text-muted small">Total Customers</div>
                            </div>
                            <div class="col-6">
                                <h3 class="font-weight-bold text-info mb-0">{{ $totalLeads }}</h3>
                                <div class="text-muted small">CRM Leads</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

