<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quote {{ $quote->number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .logo-text {
            font-size: 26px;
            font-weight: 800;
            color: #1a56db;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .company-subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
        }
        .quote-title {
            text-align: right;
            vertical-align: top;
        }
        .quote-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-draft { background-color: #f3f4f6; color: #4b5563; }
        .badge-pending_approval { background-color: #fef3c7; color: #92400e; }
        .badge-approved { background-color: #dbeafe; color: #1e40af; }
        .badge-sent { background-color: #e0e7ff; color: #3730a3; }
        .badge-accepted { background-color: #d1fae5; color: #065f46; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; }
        .badge-expired { background-color: #f3f4f6; color: #6b7280; }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .meta-box {
            width: 48%;
            vertical-align: top;
            padding: 12px 15px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
        }
        .meta-box h4 {
            margin: 0 0 8px 0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
        }
        .meta-box p {
            margin: 2px 0;
            font-size: 13px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background: #1e293b;
            color: #ffffff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            text-align: left;
        }
        .items-table td {
            padding: 11px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
        }
        .items-table tr:nth-child(even) td {
            background-color: #fcfcfc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .summary-table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .summary-table td {
            padding: 6px 12px;
            font-size: 13px;
        }
        .summary-total {
            font-size: 16px;
            font-weight: bold;
            color: #1a56db;
            border-top: 2px solid #1e293b;
            border-bottom: 2px solid #1e293b;
        }

        .notes-section {
            margin-top: 25px;
            padding: 15px;
            background: #f9fafb;
            border-left: 4px solid #1a56db;
            border-radius: 0 6px 6px 0;
            font-size: 12px;
        }
        .notes-section h5 {
            margin: 0 0 6px 0;
            font-size: 12px;
            text-transform: uppercase;
            color: #1f2937;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }
        .sig-box {
            width: 45%;
            vertical-align: top;
            padding-top: 40px;
            border-top: 1px dashed #9ca3af;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: top;">
                <div class="logo-text">SAA DIGITAL</div>
                <div class="company-subtitle">Innovative Software & Cloud Engineering</div>
                <p style="margin: 5px 0 0 0; color: #4b5563; font-size: 11px;">
                    contact@saacompany.com | +1 (555) 234-5678<br>
                    www.saacompany.com
                </p>
            </td>
            <td class="quote-title">
                <h1 style="margin: 0 0 4px 0; font-size: 26px; color: #111827;">QUOTATION</h1>
                <p style="margin: 0; font-size: 14px; font-weight: bold; color: #4b5563;">{{ $quote->number }}</p>
                <div style="margin-top: 6px;">
                    <span class="quote-badge badge-{{ $quote->effective_status }}">
                        {{ strtoupper(str_replace('_', ' ', $quote->effective_status)) }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Meta Details -->
    <table class="meta-table">
        <tr>
            <td class="meta-box">
                <h4>Quotation For</h4>
                <p style="font-weight: bold; color: #111827;">{{ $quote->recipient_name }}</p>
                @if($quote->recipient_company && $quote->recipient_company !== 'Client Organization')
                    <p style="color: #4b5563;">{{ $quote->recipient_company }}</p>
                @endif
                @if($quote->recipient_email)
                    <p style="color: #4b5563;">{{ $quote->recipient_email }}</p>
                @endif
                @if($quote->recipient_phone)
                    <p style="color: #4b5563;">{{ $quote->recipient_phone }}</p>
                @endif
            </td>
            <td style="width: 4%;"></td>
            <td class="meta-box">
                <h4>Quotation Details</h4>
                <p><strong>Issue Date:</strong> {{ $quote->created_at ? $quote->created_at->format('M d, Y') : date('M d, Y') }}</p>
                <p><strong>Valid Until:</strong> {{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : '30 Days from Issue' }}</p>
                <p><strong>Currency:</strong> {{ $quote->currency }}</p>
                @if($quote->title)
                    <p><strong>Project:</strong> {{ $quote->title }}</p>
                @endif
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
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
                <td style="color: #6b7280;">{{ $index + 1 }}</td>
                <td><strong>{{ $item->description }}</strong></td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ $quote->currency }} {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right"><strong>{{ $quote->currency }} {{ number_format($item->total, 2) }}</strong></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding: 20px; color: #9ca3af;">No line items attached.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Table -->
    <table class="summary-table">
        <tr>
            <td style="color: #4b5563;">Subtotal:</td>
            <td class="text-right"><strong>{{ $quote->currency }} {{ number_format($quote->subtotal, 2) }}</strong></td>
        </tr>
        @if($quote->discount_amount > 0)
        <tr>
            <td style="color: #047857;">
                Discount {{ $quote->discount_type === 'percentage' ? '(' . $quote->discount_rate . '%)' : '' }}:
            </td>
            <td class="text-right" style="color: #047857;">
                - {{ $quote->currency }} {{ number_format($quote->discount_amount, 2) }}
            </td>
        </tr>
        @endif
        @if($quote->tax > 0 || $quote->tax_rate > 0)
        <tr>
            <td style="color: #4b5563;">Tax ({{ $quote->tax_rate }}%):</td>
            <td class="text-right">{{ $quote->currency }} {{ number_format($quote->tax, 2) }}</td>
        </tr>
        @endif
        <tr class="summary-total">
            <td>Grand Total:</td>
            <td class="text-right">{{ $quote->currency }} {{ number_format($quote->total, 2) }}</td>
        </tr>
    </table>

    <!-- Notes & Terms -->
    @if($quote->notes)
    <div class="notes-section">
        <h5>Notes & Scope Summary</h5>
        <p style="margin: 0; white-space: pre-line;">{{ $quote->notes }}</p>
    </div>
    @endif

    @if($quote->terms)
    <div class="notes-section" style="margin-top: 15px; border-left-color: #4b5563;">
        <h5>Terms & Conditions</h5>
        <p style="margin: 0; white-space: pre-line;">{{ $quote->terms }}</p>
    </div>
    @endif

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td class="sig-box">
                Authorized SAA Engineering Representative
            </td>
            <td style="width: 10%;"></td>
            <td class="sig-box">
                Client Acceptance & Authorization (Signature & Date)
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        Thank you for considering SAA Digital Solutions. This quotation is subject to our standard service agreements.
    </div>

</body>
</html>
