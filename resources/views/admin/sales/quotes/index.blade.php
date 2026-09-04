@extends('admin.layouts.layout')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Quotes</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.quotes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Add New Quote
            </a>
        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}">Sales</a></div>
            <div class="breadcrumb-item">Quotes</div>
        </div>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-body">
                <p>Quotes will appear here.</p>
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap pb-0">
                        <!-- Filter Tabs -->
                        <ul class="nav nav-pills mb-3">
                            <li class="nav-item">
                                <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="{{ route('admin.quotes.index', ['status' => 'all', 'search' => $search]) }}">
                                    All <span class="badge badge-light ml-1">{{ $statusCounts['all'] }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status === 'draft' ? 'active' : '' }}" href="{{ route('admin.quotes.index', ['status' => 'draft', 'search' => $search]) }}">
                                    Draft <span class="badge badge-light ml-1">{{ $statusCounts['draft'] }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status === 'pending_approval' ? 'active' : '' }}" href="{{ route('admin.quotes.index', ['status' => 'pending_approval', 'search' => $search]) }}">
                                    Pending Approval <span class="badge badge-light ml-1">{{ $statusCounts['pending_approval'] }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('admin.quotes.index', ['status' => 'approved', 'search' => $search]) }}">
                                    Approved <span class="badge badge-light ml-1">{{ $statusCounts['approved'] }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status === 'sent' ? 'active' : '' }}" href="{{ route('admin.quotes.index', ['status' => 'sent', 'search' => $search]) }}">
                                    Sent <span class="badge badge-light ml-1">{{ $statusCounts['sent'] }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status === 'accepted' ? 'active' : '' }}" href="{{ route('admin.quotes.index', ['status' => 'accepted', 'search' => $search]) }}">
                                    Accepted <span class="badge badge-light ml-1">{{ $statusCounts['accepted'] }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}" href="{{ route('admin.quotes.index', ['status' => 'rejected', 'search' => $search]) }}">
                                    Rejected <span class="badge badge-light ml-1">{{ $statusCounts['rejected'] }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status === 'expired' ? 'active' : '' }}" href="{{ route('admin.quotes.index', ['status' => 'expired', 'search' => $search]) }}">
                                    Expired <span class="badge badge-light ml-1">{{ $statusCounts['expired'] }}</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Search Box -->
                        <div class="card-header-form mb-3">
                            <form method="GET" action="{{ route('admin.quotes.index') }}">
                                <input type="hidden" name="status" value="{{ $status }}">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search number, client..." value="{{ $search }}">
                                    <div class="input-group-btn">
                                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                        @if($search)
                                            <a href="{{ route('admin.quotes.index', ['status' => $status]) }}" class="btn btn-light" title="Clear search"><i class="fas fa-times"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-md mb-0">
                                <thead>
                                    <tr>
                                        <th>Quote #</th>
                                        <th>Project / Title</th>
                                        <th>Customer / Lead</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Valid Until</th>
                                        <th>Created</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($quotes as $quote)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.quotes.show', $quote) }}" class="font-weight-bold">
                                                {{ $quote->number }}
                                            </a>
                                        </td>
                                        <td>
                                            <strong>{{ $quote->title ?: 'General Project Quotation' }}</strong>
                                            <div class="text-muted small">{{ $quote->items->count() }} line item(s)</div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $quote->recipient_name }}</div>
                                            @if($quote->recipient_company && $quote->recipient_company !== 'Client Organization')
                                                <div class="text-muted small"><i class="fas fa-building mr-1"></i>{{ $quote->recipient_company }}</div>
                                            @endif
                                            @if($quote->customer_id)
                                                <span class="badge badge-light border text-primary small">Customer</span>
                                            @elseif($quote->lead_id)
                                                <span class="badge badge-light border text-info small">Lead</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold text-dark" style="font-size: 15px;">
                                            {{ $quote->currency }} {{ number_format($quote->total, 2) }}
                                        </td>
                                        <td>
                                            @php $statusName = $quote->effective_status; @endphp
                                            @if($statusName === 'accepted')
                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Accepted</span>
                                            @elseif($statusName === 'pending_approval')
                                                <span class="badge badge-warning px-2 py-1"><i class="fas fa-hourglass-half mr-1"></i>Pending Approval</span>
                                            @elseif($statusName === 'approved')
                                                <span class="badge badge-info px-2 py-1"><i class="fas fa-check mr-1"></i>Approved</span>
                                            @elseif($statusName === 'sent')
                                                <span class="badge badge-primary px-2 py-1"><i class="fas fa-paper-plane mr-1"></i>Sent</span>
                                            @elseif($statusName === 'rejected')
                                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i>Rejected</span>
                                            @elseif($statusName === 'expired')
                                                <span class="badge badge-dark px-2 py-1"><i class="fas fa-calendar-times mr-1"></i>Expired</span>
                                            @else
                                                <span class="badge badge-secondary px-2 py-1">Draft</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($quote->valid_until)
                                                <span class="{{ $quote->isExpired() ? 'text-danger font-weight-bold' : '' }}">
                                                    {{ $quote->valid_until->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted">No date</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">
                                            {{ $quote->created_at ? $quote->created_at->format('M d, Y') : '' }}
                                        </td>
                                        <td class="text-right text-nowrap">
                                            <a href="{{ route('admin.quotes.show', $quote) }}" class="btn btn-sm btn-info" title="View & Manage">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.quotes.edit', $quote) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.quotes.pdf', $quote) }}" class="btn btn-sm btn-light border" title="Download PDF">
                                                <i class="fas fa-file-pdf text-danger"></i>
                                            </a>
                                            <form action="{{ route('admin.quotes.destroy', $quote) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete quote {{ $quote->number }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted mb-3" style="font-size: 32px;"><i class="fas fa-inbox"></i></div>
                                            <h5 class="text-muted">No quotations found</h5>
                                            <p class="text-muted">There are no quotes matching your criteria.</p>
                                            <a href="{{ route('admin.quotes.create') }}" class="btn btn-primary mt-2">
                                                <i class="fas fa-plus mr-1"></i> Create First Quote
                                            </a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($quotes->hasPages())
                    <div class="card-footer text-right">
                        {{ $quotes->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
