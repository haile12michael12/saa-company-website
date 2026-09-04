@extends('frontend.layouts.layout')

@section('content')
<header class="site-header parallax-bg">
    <div class="container">
        <div class="row d-flex align-items-center">
            <div class="col-sm-8">
                <h2 class="title">Formal Quotation #{{ $quote->number }}</h2>
            </div>
            <div class="col-sm-4">
                <div class="breadcrumbs">
                    <ul>
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li>Quotation Review</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="section-padding bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Status Alerts -->
                @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm p-4 mb-4 rounded-3 text-center">
                    <div class="mb-2 text-success" style="font-size: 32px;"><i class="fal fa-check-circle"></i></div>
                    <h4 class="h5 fw-bold mb-2">Quotation Status Updated</h4>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm p-4 mb-4 rounded-3 text-center">
                    <div class="mb-2 text-danger" style="font-size: 32px;"><i class="fal fa-exclamation-circle"></i></div>
                    <h4 class="h5 fw-bold mb-2">Notice</h4>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
                @endif

                @if(session('info'))
                <div class="alert alert-info border-0 shadow-sm p-4 mb-4 rounded-3 text-center">
                    <p class="mb-0">{{ session('info') }}</p>
                </div>
                @endif

                @if($quote->status === 'accepted')
                <div class="alert alert-success border-0 shadow-sm p-4 mb-4 rounded-3 d-flex align-items-center justify-content-between flex-wrap">
                    <div>
                        <h4 class="h5 fw-bold mb-1"><i class="fal fa-check-double me-2"></i> Quotation Officially Accepted</h4>
                        <p class="mb-0 small text-muted">Accepted on {{ $quote->accepted_at ? $quote->accepted_at->format('M d, Y H:i') : 'recently' }}. Our engineering team is preparing your project onboarding.</p>
                    </div>
                    <a href="{{ route('quotes.public.pdf', $quote->token) }}" class="btn btn-outline-success font-weight-bold">
                        <i class="fal fa-file-pdf me-1"></i> Download Accepted PDF
                    </a>
                </div>
                @elseif($quote->status === 'rejected')
                <div class="alert alert-danger border-0 shadow-sm p-4 mb-4 rounded-3">
                    <h4 class="h5 fw-bold mb-1"><i class="fal fa-times-circle me-2"></i> Quotation Declined</h4>
                    <p class="mb-0 small">This quotation was marked as declined. Please contact our team if you wish to adjust the project parameters.</p>
                </div>
                @elseif($quote->isExpired())
                <div class="alert alert-warning border-0 shadow-sm p-4 mb-4 rounded-3">
                    <h4 class="h5 fw-bold mb-1"><i class="fal fa-hourglass-end me-2"></i> Quotation Validity Expired</h4>
                    <p class="mb-0 small">The validity period for this quote ended on {{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : 'earlier' }}. Please contact us for an updated revision.</p>
                </div>
                @endif

                <!-- Main Quotation Document Card -->
                <div class="card border-0 shadow-sm p-4 p-md-5 mb-4" style="border-radius: 16px; background: #ffffff;">
                    <!-- Document Header -->
                    <div class="d-flex justify-content-between align-items-start flex-wrap border-bottom pb-4 mb-4">
                        <div>
                            <h3 class="fw-bold text-dark mb-1">SAA DIGITAL SOLUTIONS</h3>
                            <p class="text-muted small mb-0">High-Performance Engineering & Digital Platforms</p>
                            <p class="text-muted small mb-0">contact@saacompany.com | +1 (555) 234-5678</p>
                        </div>
                        <div class="text-md-end mt-3 mt-md-0">
                            <span class="badge bg-primary px-3 py-2 text-uppercase mb-2" style="font-size: 13px;">
                                Formal Estimate #{{ $quote->number }}
                            </span>
                            <div class="text-muted small"><strong>Issue Date:</strong> {{ $quote->created_at ? $quote->created_at->format('M d, Y') : date('M d, Y') }}</div>
                            <div class="text-muted small"><strong>Valid Until:</strong> {{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : '30 Days' }}</div>
                            <div class="mt-2">
                                <a href="{{ route('quotes.public.pdf', $quote->token) }}" class="btn btn-sm btn-outline-danger">
                                    <i class="fal fa-file-pdf me-1"></i> Download PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Client Info & Project Title -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted fw-bold small mb-2">Prepared For:</h6>
                            <h5 class="fw-bold text-dark mb-1">{{ $quote->recipient_name }}</h5>
                            @if($quote->recipient_company && $quote->recipient_company !== 'Client Organization')
                                <div class="text-muted small mb-1">{{ $quote->recipient_company }}</div>
                            @endif
                            @if($quote->recipient_email)
                                <div class="text-muted small">{{ $quote->recipient_email }}</div>
                            @endif
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-uppercase text-muted fw-bold small mb-2">Project Brief:</h6>
                            <h5 class="fw-bold text-dark mb-1">{{ $quote->title ?: 'Custom Engineering Project' }}</h5>
                            <span class="text-muted small">Currency: <strong>{{ $quote->currency }}</strong></span>
                        </div>
                    </div>

                    <!-- Attached Proposal / Scope Narrative (If exists) -->
                    @if($quote->proposals->count() > 0)
                    @php $primaryProposal = $quote->proposals->last(); @endphp
                    <div class="p-4 mb-4 rounded-3 border bg-light">
                        <h5 class="fw-bold text-dark mb-3"><i class="fal fa-scroll me-2 text-primary"></i> Proposal Overview: {{ $primaryProposal->title }}</h5>
                        <div class="proposal-content text-muted small" style="white-space: pre-line; line-height: 1.7;">
                            {!! nl2br(e($primaryProposal->content)) !!}
                        </div>
                    </div>
                    @endif

                    <!-- Line Items Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 8%;">#</th>
                                    <th style="width: 52%;">Deliverable / Item Description</th>
                                    <th class="text-center" style="width: 10%;">Qty</th>
                                    <th class="text-end" style="width: 15%;">Unit Price</th>
                                    <th class="text-end" style="width: 15%;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quote->items as $idx => $it)
                                <tr>
                                    <td class="text-muted">{{ $idx + 1 }}</td>
                                    <td><strong>{{ $it->description }}</strong></td>
                                    <td class="text-center">{{ $it->quantity }}</td>
                                    <td class="text-end">{{ $quote->currency }} {{ number_format($it->unit_price, 2) }}</td>
                                    <td class="text-end fw-bold">{{ $quote->currency }} {{ number_format($it->total, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">No line items specified.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Breakdown -->
                    <div class="row justify-content-end mb-4">
                        <div class="col-md-5">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Subtotal:</span>
                                    <span class="fw-bold">{{ $quote->currency }} {{ number_format($quote->subtotal, 2) }}</span>
                                </div>
                                @if($quote->discount_amount > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span class="small">Discount {{ $quote->discount_type === 'percentage' ? '(' . $quote->discount_rate . '%)' : '' }}:</span>
                                    <span class="fw-bold">- {{ $quote->currency }} {{ number_format($quote->discount_amount, 2) }}</span>
                                </div>
                                @endif
                                @if($quote->tax > 0 || $quote->tax_rate > 0)
                                <div class="d-flex justify-content-between mb-2 text-muted">
                                    <span class="small">Tax ({{ $quote->tax_rate }}%):</span>
                                    <span class="fw-bold">+ {{ $quote->currency }} {{ number_format($quote->tax, 2) }}</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between pt-2 border-top">
                                    <span class="h5 fw-bold text-dark mb-0">Total Investment:</span>
                                    <span class="h5 fw-bold text-primary mb-0">{{ $quote->currency }} {{ number_format($quote->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Conditions -->
                    @if($quote->terms)
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2">Commercial Terms & Warranty:</h6>
                        <p class="text-muted small mb-0" style="white-space: pre-line;">{{ $quote->terms }}</p>
                    </div>
                    @endif

                    <!-- Action Area: Acceptance & Rejection Forms -->
                    @if($quote->status !== 'accepted' && $quote->status !== 'rejected' && !$quote->isExpired())
                    <div class="border-top pt-4 mt-4">
                        <div class="row g-4">
                            <!-- Accept Column -->
                            <div class="col-md-7">
                                <div class="card border-success border-2 p-4 h-100" style="border-radius: 12px; background: #f0fdf4;">
                                    <h5 class="fw-bold text-success mb-2"><i class="fal fa-check-circle me-2"></i> Accept & Authorize Quote</h5>
                                    <p class="small text-muted mb-3">By accepting this estimate, you authorize SAA Digital Solutions to initiate project onboarding according to the terms specified.</p>

                                    <form action="{{ route('quotes.public.accept', $quote->token) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Signer Full Name *</label>
                                            <input type="text" name="signer_name" class="form-control" placeholder="e.g. {{ $quote->recipient_name }}" value="{{ old('signer_name', $quote->recipient_name) }}" required>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="agreement" id="agreeCheck" required>
                                            <label class="form-check-label small text-muted" for="agreeCheck">
                                                I confirm I am authorized to accept this quotation and agree to the specified terms and scope.
                                            </label>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-lg w-100 font-weight-bold shadow-sm">
                                            <i class="fal fa-file-signature me-2"></i> Accept Quotation ({{ $quote->currency }} {{ number_format($quote->total, 2) }})
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Reject Column -->
                            <div class="col-md-5">
                                <div class="card border-0 p-4 bg-light h-100" style="border-radius: 12px;">
                                    <h6 class="fw-bold text-dark mb-2"><i class="fal fa-times-circle me-2 text-danger"></i> Need Changes or Decline?</h6>
                                    <p class="small text-muted mb-3">If this estimate does not align with your expectations or budget, let us know:</p>

                                    <form action="{{ route('quotes.public.reject', $quote->token) }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Reason / Feedback *</label>
                                            <textarea name="reason" class="form-control form-control-sm" rows="3" placeholder="e.g. Budget exceeds allocation, scope adjustment needed..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                            Decline Quotation
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

