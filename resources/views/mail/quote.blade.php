<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quote {{ $quote->number }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 20px;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 25px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 30px;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .meta-row:last-child {
            margin-bottom: 0;
        }
        .total-row {
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            font-weight: bold;
            font-size: 16px;
            color: #1e3a8a;
        }
        .btn-wrapper {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SAA DIGITAL SOLUTIONS</h1>
            <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.9;">Formal Quotation #{{ $quote->number }}</p>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $quote->recipient_name }}</strong>,</p>

            @if(!empty($customMessage))
                <p>{{ $customMessage }}</p>
            @else
                <p>Thank you for giving us the opportunity to submit our proposal and quotation for your upcoming project.</p>
                <p>We have carefully evaluated your requirements and prepared the following estimate for your review:</p>
            @endif

            <div class="meta-box">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <tr>
                        <td style="padding: 4px 0; color: #64748b;">Quote Number:</td>
                        <td style="padding: 4px 0; text-align: right; font-weight: 600;">{{ $quote->number }}</td>
                    </tr>
                    @if($quote->title)
                    <tr>
                        <td style="padding: 4px 0; color: #64748b;">Project Title:</td>
                        <td style="padding: 4px 0; text-align: right;">{{ $quote->title }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 4px 0; color: #64748b;">Valid Until:</td>
                        <td style="padding: 4px 0; text-align: right;">{{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : '30 Days' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 0; color: #64748b;">Subtotal:</td>
                        <td style="padding: 4px 0; text-align: right;">{{ $quote->currency }} {{ number_format($quote->subtotal, 2) }}</td>
                    </tr>
                    @if($quote->discount_amount > 0)
                    <tr>
                        <td style="padding: 4px 0; color: #16a34a;">Discount:</td>
                        <td style="padding: 4px 0; text-align: right; color: #16a34a;">- {{ $quote->currency }} {{ number_format($quote->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($quote->tax > 0)
                    <tr>
                        <td style="padding: 4px 0; color: #64748b;">Tax ({{ $quote->tax_rate }}%):</td>
                        <td style="padding: 4px 0; text-align: right;">{{ $quote->currency }} {{ number_format($quote->tax, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="border-top: 1px solid #cbd5e1;">
                        <td style="padding: 8px 0 0 0; font-weight: bold; font-size: 16px; color: #1e3a8a;">Grand Total:</td>
                        <td style="padding: 8px 0 0 0; text-align: right; font-weight: bold; font-size: 16px; color: #1e3a8a;">
                            {{ $quote->currency }} {{ number_format($quote->total, 2) }}
                        </td>
                    </tr>
                </table>
            </div>

            @if($quote->token)
            <div class="btn-wrapper">
                <a href="{{ route('quotes.public.show', $quote->token) }}" class="btn">
                    View & Accept Quotation Online
                </a>
            </div>
            <p style="font-size: 12px; color: #64748b; text-align: center;">
                Or copy and paste this link into your browser:<br>
                <a href="{{ route('quotes.public.show', $quote->token) }}" style="color: #2563eb;">{{ route('quotes.public.show', $quote->token) }}</a>
            </p>
            @endif

            <p style="margin-top: 25px;">A PDF copy of this quotation has been generated and is attached to this email for your convenience.</p>

            <p>If you have any questions or require revisions, please do not hesitate to contact us.</p>

            <p style="margin-bottom: 0;">Warm regards,<br>
            <strong>SAA Digital Solutions Team</strong></p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SAA Digital Solutions. All rights reserved.<br>
            contact@saacompany.com | +1 (555) 234-5678
        </div>
    </div>
</body>
</html>

