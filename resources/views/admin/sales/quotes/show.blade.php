@extends('admin.layouts.layout')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Quote #{{ $quote->number }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}">Sales</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.quotes.index') }}">Quotes</a></div>
            <div class="breadcrumb-item">{{ $quote->number }}</div>
        </div>
    </div>

    <div class="section-body">
        <!-- Status & Primary Action Banner -->
        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap py-3">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <span class="mr-3 font-weight-bold text-dark" style="font-size: 16px;">
                        {{ $quote->title ?: 'Quotation' }}
                    </span>
                    @php $status = $quote->effective_status; @endphp
                    @if($status === 'accepted')
                        <span class="badge badge-success px-3 py-2" style="font-size: 14px;"><i class="fas fa-check-circle mr-1"></i> Accepted</span>
                    @elseif($status === 'pending_approval')
                        <span class="badge badge-warning px-3 py-2" style="font-size: 14px;"><i class="fas fa-hourglass-half mr-1"></i> Pending Approval</span>
                    @elseif($status === 'approved')
                        <span class="badge badge-info px-3 py-2" style="font-size: 14px;"><i class="fas fa-check mr-1"></i> Approved</span>
                    @elseif($status === 'sent')
                        <span class="badge badge-primary px-3 py-2" style="font-size: 14px;"><i class="fas fa-paper-plane mr-1"></i> Sent</span>
                    @elseif($status === 'rejected')
                        <span class="badge badge-danger px-3 py-2" style="font-size: 14px;"><i class="fas fa-times-circle mr-1"></i> Rejected</span>
                    @elseif($status === 'expired')
                        <span class="badge badge-dark px-3 py-2" style="font-size: 14px;"><i class="fas fa-calendar-times mr-1"></i> Expired</span>
                    @else
                        <span class="badge badge-secondary px-3 py-2" style="font-size: 14px;">Draft</span>
                    @endif

                    @if($quote->isExpired())
                        <span class="badge badge-danger ml-2 px-2 py-1"><i class="fas fa-exclamation-triangle mr-1"></i> Validity Ended</span>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-wrap">
                    @if($quote->canBeApproved())
                    <form action="{{ route('admin.quotes.approve', $quote) }}" method="POST" class="mr-2 mb-1">
                        @csrf
                        <button type="submit" class="btn btn-info font-weight-bold shadow-sm">
                            <i class="fas fa-check mr-1"></i> Approve Quote
                        </button>
                    </form>
                    @endif

                    <button type="button" class="btn btn-primary mr-2 mb-1 shadow-sm" data-toggle="modal" data-target="#sendEmailModal">
                        <i class="fas fa-paper-plane mr-1"></i> Send Email
                    </button>

                    <a href="{{ route('admin.quotes.pdf', $quote) }}" class="btn btn-light border mr-2 mb-1 shadow-sm">
                        <i class="fas fa-file-pdf text-danger mr-1"></i> Download PDF
                    </a>

                    <button type="button" class="btn btn-outline-info mr-2 mb-1 shadow-sm" data-toggle="modal" data-target="#createProposalModal">
                        <i class="fas fa-scroll mr-1"></i> Proposal
                    </button>

                    <a href="{{ route('admin.quotes.edit', $quote) }}" class="btn btn-warning mr-2 mb-1 shadow-sm">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>

                    <div class="dropdown d-inline mb-1">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="moreActionsMenu" data-toggle="dropdown">
                            More
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            @if($quote->status !== 'accepted')
                            <form action="{{ route('admin.quotes.accept', $quote) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-success">
                                    <i class="fas fa-check-circle mr-2"></i> Mark Accepted
                                </button>
                            </form>
                            @endif

                            @if($quote->status !== 'rejected')
                            <a href="#" class="dropdown-item text-danger" data-toggle="modal" data-target="#rejectModal">
                                <i class="fas fa-times-circle mr-2"></i> Mark Rejected
                            </a>
                            @endif

                            <div class="dropdown-divider"></div>

                            <form action="{{ route('admin.quotes.destroy', $quote) }}" method="POST" onsubmit="return confirm('Permanently delete this quote?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash mr-2"></i> Delete Quote
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversion Highlights Banner (When Applicable) -->
        @if($quote->canBeConvertedToCustomer() || $quote->canBeConvertedToProject() || $quote->project)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: #ffffff;">
                    <div class="card-body py-3 d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h5 class="text-white mb-1"><i class="fas fa-rocket mr-2"></i> Business Conversion Hub</h5>
                            <p class="mb-0 text-light small">Seamlessly transition this deal into formal customer records and project operations.</p>
                        </div>
                        <div class="d-flex flex-wrap">
                            @if($quote->canBeConvertedToCustomer())
                            <form action="{{ route('admin.quotes.convert-to-customer', $quote) }}" method="POST" class="mr-2 mb-1">
                                @csrf
                                <button type="submit" class="btn btn-warning font-weight-bold shadow">
                                    <i class="fas fa-user-plus mr-1"></i> Convert to Customer
                                </button>
                            </form>
                            @endif

                            @if($quote->canBeConvertedToProject())
                            <form action="{{ route('admin.quotes.convert-to-project', $quote) }}" method="POST" class="mb-1">
                                @csrf
                                <button type="submit" class="btn btn-success font-weight-bold shadow">
                                    <i class="fas fa-project-diagram mr-1"></i> Convert to Project (${{ number_format($quote->total, 2) }})
                                </button>
                            </form>
                            @endif

                            @if($quote->project)
                            <span class="badge badge-light text-dark p-2 font-weight-bold">
                                <i class="fas fa-check-circle text-success mr-1"></i> Linked Project: {{ $quote->project->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <!-- Left Side: Items & Financials -->
            <div class="col-lg-8">
                <!-- Line Items Card -->
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-list-alt mr-2 text-primary"></i> Line Items</h4>
                        <span class="badge badge-light border">{{ $quote->items->count() }} item(s)</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 8%;">#</th>
                                        <th style="width: 52%;">Description</th>
                                        <th class="text-center" style="width: 10%;">Qty</th>
                                        <th class="text-right" style="width: 15%;">Unit Price</th>
                                        <th class="text-right" style="width: 15%;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($quote->items as $index => $item)
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td><strong>{{ $item->description }}</strong></td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-right">{{ $quote->currency }} {{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-right font-weight-bold">{{ $quote->currency }} {{ number_format($item->total, 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No line items.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Totals Breakdown Footer -->
                    <div class="card-footer bg-light">
                        <div class="row justify-content-end">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted">Subtotal:</td>
                                        <td class="text-right font-weight-bold">{{ $quote->currency }} {{ number_format($quote->subtotal, 2) }}</td>
                                    </tr>
                                    @if($quote->discount_amount > 0)
                                    <tr class="text-success">
                                        <td>
                                            Discount {{ $quote->discount_type === 'percentage' ? '(' . $quote->discount_rate . '%)' : '' }}:
                                        </td>
                                        <td class="text-right font-weight-bold">- {{ $quote->currency }} {{ number_format($quote->discount_amount, 2) }}</td>
                                    </tr>
                                    @endif
                                    @if($quote->tax > 0 || $quote->tax_rate > 0)
                                    <tr>
                                        <td class="text-muted">Tax ({{ $quote->tax_rate }}%):</td>
                                        <td class="text-right font-weight-bold">{{ $quote->currency }} {{ number_format($quote->tax, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr class="border-top" style="font-size: 18px;">
                                        <td class="font-weight-bold text-dark">Grand Total:</td>
                                        <td class="text-right font-weight-bold text-primary">{{ $quote->currency }} {{ number_format($quote->total, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes & Terms -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4><i class="fas fa-file-contract mr-2 text-primary"></i> Specifications & Contract Terms</h4>
                    </div>
                    <div class="card-body">
                        @if($quote->notes)
                            <h6 class="font-weight-bold text-dark mb-2">Scope & Project Notes:</h6>
                            <p class="text-muted mb-4" style="white-space: pre-line;">{{ $quote->notes }}</p>
                        @endif

                        @if($quote->terms)
                            <h6 class="font-weight-bold text-dark mb-2">Commercial & Delivery Terms:</h6>
                            <p class="text-muted mb-0" style="white-space: pre-line;">{{ $quote->terms }}</p>
                        @endif

                        @if(!$quote->notes && !$quote->terms)
                            <p class="text-muted mb-0 font-italic">No custom notes or terms specified.</p>
                        @endif
                    </div>
                </div>

                <!-- Attached Proposals -->
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-scroll mr-2 text-info"></i> Attached Proposals</h4>
                        <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#createProposalModal">
                            <i class="fas fa-plus mr-1"></i> Generate Proposal
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($quote->proposals as $prop)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 font-weight-bold">{{ $prop->title }}</h6>
                                    <div class="text-muted small">
                                        Template: {{ $prop->template->name ?? 'Custom Narrative' }} |
                                        Created: {{ $prop->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                                <div>
                                    <span class="badge badge-light border mr-2">{{ ucfirst($prop->status) }}</span>
                                    <a href="{{ route('quotes.public.show', $quote->token) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Preview Online">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </li>
                            @empty
                            <li class="list-group-item text-center text-muted py-4">
                                No proposals attached yet. Click "Generate Proposal" to create one from a template.
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Side: Contact Details & Client Portal -->
            <div class="col-lg-4">
                <!-- Recipient Card -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4><i class="fas fa-user-tie mr-2 text-primary"></i> Recipient Info</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="text-dark font-weight-bold mb-1">{{ $quote->recipient_name }}</h5>
                        @if($quote->recipient_company && $quote->recipient_company !== 'Client Organization')
                            <div class="text-muted mb-3"><i class="fas fa-building mr-1"></i> {{ $quote->recipient_company }}</div>
                        @endif

                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <span class="text-muted small">Email:</span><br>
                                <strong class="text-dark">{{ $quote->recipient_email ?: 'No email specified' }}</strong>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted small">Phone:</span><br>
                                <strong class="text-dark">{{ $quote->recipient_phone ?: 'N/A' }}</strong>
                            </li>
                            <li class="mb-2">
                                <span class="text-muted small">Quotation Origin:</span><br>
                                @if($quote->customer)
                                    <span class="badge badge-primary">Existing Customer</span>
                                @elseif($quote->lead)
                                    <span class="badge badge-info">CRM Lead ({{ $quote->lead->source }})</span>
                                @else
                                    <span class="badge badge-secondary">Direct</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Timeline / Audit Info -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4><i class="fas fa-history mr-2 text-primary"></i> Quote Timeline</h4>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Created:</span>
                                <span>{{ $quote->created_at ? $quote->created_at->format('M d, Y H:i') : 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Valid Until:</span>
                                <span class="{{ $quote->isExpired() ? 'text-danger font-weight-bold' : '' }}">
                                    {{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : 'N/A' }}
                                </span>
                            </li>
                            @if($quote->approved_at)
                            <li class="list-group-item d-flex justify-content-between bg-light">
                                <span class="text-info font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Approved:</span>
                                <span>{{ $quote->approved_at->format('M d, Y H:i') }}</span>
                            </li>
                            @endif
                            @if($quote->sent_at)
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-primary font-weight-bold"><i class="fas fa-paper-plane mr-1"></i> Sent:</span>
                                <span>{{ $quote->sent_at->format('M d, Y H:i') }}</span>
                            </li>
                            @endif
                            @if($quote->accepted_at)
                            <li class="list-group-item d-flex justify-content-between bg-success text-white">
                                <span><i class="fas fa-check-double mr-1"></i> Accepted:</span>
                                <span>{{ $quote->accepted_at->format('M d, Y H:i') }}</span>
                            </li>
                            @endif
                            @if($quote->rejected_at)
                            <li class="list-group-item bg-danger text-white">
                                <div class="d-flex justify-content-between">
                                    <span><i class="fas fa-times-circle mr-1"></i> Rejected:</span>
                                    <span>{{ $quote->rejected_at->format('M d, Y H:i') }}</span>
                                </div>
                                @if($quote->rejection_reason)
                                    <div class="mt-1 small">Reason: {{ $quote->rejection_reason }}</div>
                                @endif
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Client Online Link -->
                @if($quote->token)
                <div class="card shadow-sm border-info">
                    <div class="card-header bg-info text-white">
                        <h4 class="text-white"><i class="fas fa-link mr-2"></i> Client Online Portal</h4>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">Share this secure link with your client for them to review, download PDF, and accept or decline online:</p>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-sm" id="client-link-input"
                                value="{{ route('quotes.public.show', $quote->token) }}" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-outline-info" type="button" onclick="copyClientLink()">
                                    Copy
                                </button>
                            </div>
                        </div>
                        <a href="{{ route('quotes.public.show', $quote->token) }}" target="_blank" class="btn btn-sm btn-outline-primary btn-block">
                            <i class="fas fa-external-link-alt mr-1"></i> Open Client Portal View
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Modal: Send Email -->
<div class="modal fade" id="sendEmailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('admin.quotes.send', $quote) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-envelope mr-2 text-primary"></i> Send Quotation Email</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Recipient</label>
                    <input type="text" class="form-control" value="{{ $quote->recipient_email }}" readonly>
                    <small class="text-muted">Quotation PDF and online review link will be attached automatically.</small>
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold">Custom Note for Client (Optional)</label>
                    <textarea name="email_message" class="form-control" rows="3" placeholder="Add a personalized message or greeting..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-paper-plane mr-1"></i> Send Now</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reject Quote -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('admin.quotes.reject', $quote) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white"><i class="fas fa-times-circle mr-2"></i> Reject Quote</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-0">
                    <label class="font-weight-bold">Rejection Reason / Feedback</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="e.g. Budget constraints, project postponed, selected competitor..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger font-weight-bold">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Generate Proposal -->
<div class="modal fade" id="createProposalModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form action="{{ route('admin.proposals.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="quote_id" value="{{ $quote->id }}">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white"><i class="fas fa-scroll mr-2"></i> Generate Proposal from Template</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold">Proposal Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="Comprehensive Proposal for {{ $quote->recipient_name }}" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">Select Proposal Template</label>
                    <select name="template_id" class="form-control" id="template-picker">
                        <option value="">-- Custom (Blank Content) --</option>
                        @foreach($proposalTemplates as $tpl)
                        <option value="{{ $tpl->id }}">
                            {{ $tpl->name }} ({{ $tpl->category }})
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Template placeholders like @{{client_name}}, @{{quote_number}}, and @{{total_amount}} will be populated automatically.</small>
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold">Custom Content / Additional Scope (Optional)</label>
                    <textarea name="content" class="form-control" rows="5" placeholder="Leave empty to use the selected template verbatim, or provide custom proposal narrative..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-info font-weight-bold"><i class="fas fa-check mr-1"></i> Generate Proposal</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function copyClientLink() {
    let copyText = document.getElementById("client-link-input");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    toastr.success('Client portal link copied to clipboard!');
}
</script>
@endpush
@endsection

