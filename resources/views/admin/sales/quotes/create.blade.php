@extends('admin.layouts.layout')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{ !empty($isEdit) ? 'Edit Quote #' . $quote->number : 'Create Quotation' }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}">Sales</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.quotes.index') }}">Quotes</a></div>
            <div class="breadcrumb-item">{{ !empty($isEdit) ? 'Edit' : 'Create' }}</div>
        </div>
    </div>

    <div class="section-body">
        <form action="{{ !empty($isEdit) ? route('admin.quotes.update', $quote) : route('admin.quotes.store') }}" method="POST" id="quote-form">
            @csrf
            @if(!empty($isEdit))
                @method('PUT')
            @endif

            <div class="row">
                <!-- Left Column: Details & Items -->
                <div class="col-lg-8">
                    <!-- General Quote Meta -->
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4><i class="fas fa-file-invoice mr-2 text-primary"></i> 1. Quotation Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Quote Number <span class="text-danger">*</span></label>
                                    <input type="text" name="number" class="form-control @error('number') is-invalid @enderror"
                                        value="{{ old('number', $quote->number ?? $nextNumber) }}" required>
                                    @error('number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="font-weight-bold">Valid Until (Expiration Date)</label>
                                    <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror"
                                        value="{{ old('valid_until', isset($quote->valid_until) ? $quote->valid_until->format('Y-m-d') : date('Y-m-d', strtotime('+30 days'))) }}">
                                    @error('valid_until')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8 form-group">
                                    <label class="font-weight-bold">Project / Scope Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. Enterprise Cloud SaaS & Mobile Application"
                                        value="{{ old('title', $quote->title ?? ($selectedLead ? 'Quotation for ' . $selectedLead->name : '')) }}">
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="font-weight-bold">Currency</label>
                                    <select name="currency" class="form-control" id="currency-select">
                                        <option value="USD" {{ old('currency', $quote->currency ?? 'USD') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="EUR" {{ old('currency', $quote->currency ?? '') === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                        <option value="GBP" {{ old('currency', $quote->currency ?? '') === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                        <option value="CAD" {{ old('currency', $quote->currency ?? '') === 'CAD' ? 'selected' : '' }}>CAD ($)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Line Items Repeater -->
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4><i class="fas fa-list-ol mr-2 text-primary"></i> 2. Line Items</h4>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-item">
                                <i class="fas fa-plus mr-1"></i> Add Row
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="items-table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 50%;">Item / Deliverable Description <span class="text-danger">*</span></th>
                                            <th style="width: 15%;" class="text-center">Qty <span class="text-danger">*</span></th>
                                            <th style="width: 20%;" class="text-right">Unit Price <span class="text-danger">*</span></th>
                                            <th style="width: 15%;" class="text-right">Total</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-tbody">
                                        @php
                                            $existingItems = old('items', isset($quote) ? $quote->items->toArray() : [
                                                ['description' => $selectedLead ? ($selectedLead->source === 'Quote Request' ? 'Custom Development & Engineering Sprint' : 'Consulting & Implementation') : 'Custom Software Engineering Services', 'quantity' => 1, 'unit_price' => 2500.00]
                                            ]);
                                        @endphp

                                        @foreach($existingItems as $idx => $it)
                                        <tr class="item-row">
                                            <td>
                                                <input type="text" name="items[{{ $idx }}][description]" class="form-control item-desc"
                                                    placeholder="Service or deliverable brief..." value="{{ $it['description'] ?? '' }}" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $idx }}][quantity]" class="form-control text-center item-qty"
                                                    min="1" step="1" value="{{ $it['quantity'] ?? 1 }}" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $idx }}][unit_price]" class="form-control text-right item-price"
                                                    min="0" step="0.01" value="{{ $it['unit_price'] ?? 0 }}" required>
                                            </td>
                                            <td class="text-right font-weight-bold pt-3 item-total-display">
                                                $0.00
                                            </td>
                                            <td class="text-center pt-2">
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove Item">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Notes & Terms -->
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4><i class="fas fa-file-contract mr-2 text-primary"></i> 3. Terms & Notes</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="font-weight-bold">Notes & Specifications</label>
                                <textarea name="notes" class="form-control" rows="4" placeholder="Additional notes or specifications for the client...">{{ old('notes', $quote->notes ?? ($selectedLead ? $selectedLead->notes : '')) }}</textarea>
                            </div>
                            <div class="form-group mb-0">
                                <label class="font-weight-bold">Terms & Conditions</label>
                                <textarea name="terms" class="form-control" rows="3" placeholder="Payment milestones, warranty, deliverable terms...">{{ old('terms', $quote->terms ?? "1. 50% initial deposit upon signature, 50% upon final acceptance.\n2. Includes 30 days post-launch technical support and maintenance.\n3. Source code and IP transferred in full upon final payment.") }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Recipient & Financial Summary -->
                <div class="col-lg-4">
                    <!-- Client Selection -->
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h4><i class="fas fa-user-check mr-2 text-primary"></i> Client / Lead Origin</h4>
                        </div>
                        <div class="card-body">
                            <!-- CRM Lead Selection -->
                            <div class="form-group">
                                <label class="font-weight-bold">Originate from CRM Lead</label>
                                <select name="lead_id" class="form-control" id="lead-select">
                                    <option value="">-- No Lead (Or Select Lead) --</option>
                                    @foreach($leads as $lead)
                                    <option value="{{ $lead->id }}" {{ (old('lead_id', $quote->lead_id ?? '') == $lead->id || ($selectedLead && $selectedLead->id == $lead->id)) ? 'selected' : '' }}>
                                        {{ $lead->name }} ({{ $lead->email ?: 'No email' }})
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Links this quotation directly to a CRM prospect.</small>
                            </div>

                            <div class="text-center font-weight-bold text-muted my-2">-- OR --</div>

                            <!-- Customer Selection -->
                            <div class="form-group">
                                <label class="font-weight-bold">Existing Customer</label>
                                <select name="customer_id" class="form-control" id="customer-select">
                                    <option value="">-- Select Existing Customer --</option>
                                    @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}" {{ (old('customer_id', $quote->customer_id ?? '') == $cust->id || ($selectedCustomer && $selectedCustomer->id == $cust->id)) ? 'selected' : '' }}>
                                        {{ $cust->name }} ({{ $cust->email }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($selectedLead)
                            <div class="alert alert-info py-2 px-3 small mt-3 mb-0">
                                <strong><i class="fas fa-info-circle mr-1"></i> Lead Contact:</strong><br>
                                {{ $selectedLead->name }}<br>
                                {{ $selectedLead->email }} | {{ $selectedLead->phone }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Financial Summary & Calculation Box -->
                    <div class="card shadow-sm border-primary">
                        <div class="card-header bg-primary text-white">
                            <h4 class="text-white"><i class="fas fa-calculator mr-2"></i> Financial Summary</h4>
                        </div>
                        <div class="card-body">
                            <!-- Discount Controls -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small">Discount</label>
                                <div class="input-group">
                                    <select name="discount_type" class="form-control col-5" id="discount-type">
                                        <option value="fixed" {{ old('discount_type', $quote->discount_type ?? 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed ($)</option>
                                        <option value="percentage" {{ old('discount_type', $quote->discount_type ?? '') === 'percentage' ? 'selected' : '' }}>Percent (%)</option>
                                    </select>
                                    <input type="number" name="discount_rate" id="discount-rate" class="form-control text-right"
                                        min="0" step="0.01" value="{{ old('discount_rate', $quote->discount_rate ?? 0) }}">
                                </div>
                            </div>

                            <!-- Tax Rate -->
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small">Tax Rate (%)</label>
                                <div class="input-group">
                                    <input type="number" name="tax_rate" id="tax-rate" class="form-control text-right"
                                        min="0" max="100" step="0.1" value="{{ old('tax_rate', $quote->tax_rate ?? 0) }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Breakdown Table -->
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span class="font-weight-bold" id="display-subtotal">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Discount:</span>
                                <span class="font-weight-bold" id="display-discount">- $0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 text-muted">
                                <span>Tax Amount:</span>
                                <span class="font-weight-bold" id="display-tax">+ $0.00</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2 border-top" style="font-size: 18px;">
                                <span class="font-weight-bold text-dark">Grand Total:</span>
                                <span class="font-weight-bold text-primary" id="display-total">$0.00</span>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            @if(empty($isEdit))
                                <button type="submit" name="submit_action" value="save_draft" class="btn btn-secondary btn-block mb-2 font-weight-bold">
                                    <i class="fas fa-save mr-1"></i> Save as Draft
                                </button>
                                <button type="submit" name="submit_action" value="submit_approval" class="btn btn-primary btn-block font-weight-bold shadow-sm">
                                    <i class="fas fa-paper-plane mr-1"></i> Submit for Approval
                                </button>
                            @else
                                <button type="submit" class="btn btn-success btn-block btn-lg font-weight-bold shadow-sm">
                                    <i class="fas fa-check mr-1"></i> Update Quotation
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let rowIndex = {{ count($existingItems) }};

    function recalculate() {
        let subtotal = 0;

        $('.item-row').each(function() {
            let qty = parseFloat($(this).find('.item-qty').val()) || 0;
            let price = parseFloat($(this).find('.item-price').val()) || 0;
            let lineTotal = qty * price;

            $(this).find('.item-total-display').text('$' + lineTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            subtotal += lineTotal;
        });

        let discType = $('#discount-type').val();
        let discRate = parseFloat($('#discount-rate').val()) || 0;
        let discount = 0;

        if (discType === 'percentage') {
            discount = subtotal * (discRate / 100);
        } else {
            discount = discRate;
        }
        if (discount > subtotal) discount = subtotal;

        let taxable = Math.max(0, subtotal - discount);
        let taxRate = parseFloat($('#tax-rate').val()) || 0;
        let tax = taxable * (taxRate / 100);

        let grandTotal = taxable + tax;

        $('#display-subtotal').text('$' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#display-discount').text('- $' + discount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#display-tax').text('+ $' + tax.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#display-total').text('$' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    }

    // Add row
    $('#btn-add-item').on('click', function() {
        let newRow = `
            <tr class="item-row">
                <td>
                    <input type="text" name="items[${rowIndex}][description]" class="form-control item-desc" placeholder="Service or deliverable brief..." required>
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][quantity]" class="form-control text-center item-qty" min="1" step="1" value="1" required>
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][unit_price]" class="form-control text-right item-price" min="0" step="0.01" value="0" required>
                </td>
                <td class="text-right font-weight-bold pt-3 item-total-display">$0.00</td>
                <td class="text-center pt-2">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove Item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#items-tbody').append(newRow);
        rowIndex++;
        recalculate();
    });

    // Remove row
    $(document).on('click', '.btn-remove-row', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('tr').remove();
            recalculate();
        } else {
            alert('A quote must contain at least one line item.');
        }
    });

    // Input events
    $(document).on('input change', '.item-qty, .item-price, #discount-type, #discount-rate, #tax-rate', function() {
        recalculate();
    });

    // Initial calculation
    recalculate();
});
</script>
@endpush

