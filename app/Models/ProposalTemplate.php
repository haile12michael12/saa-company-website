<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'subject',
        'category',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'template_id');
    }

    /**
     * Interpolate template placeholders using a Quote model instance.
     */
    public function render(Quote $quote): string
    {
        $recipientName = $quote->customer->name ?? ($quote->lead->name ?? 'Valued Client');
        $recipientEmail = $quote->customer->email ?? ($quote->lead->email ?? '');
        $recipientPhone = $quote->customer->phone ?? ($quote->lead->phone ?? '');
        $recipientCompany = $quote->customer->name ?? ($quote->lead->notes ? self::extractCompany($quote->lead->notes) : 'Client Organization');

        // Build HTML table for items
        $itemsHtml = '<table style="width: 100%; border-collapse: collapse; margin: 15px 0;">';
        $itemsHtml .= '<thead><tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">';
        $itemsHtml .= '<th style="padding: 10px; text-align: left;">Item Description</th>';
        $itemsHtml .= '<th style="padding: 10px; text-align: center;">Qty</th>';
        $itemsHtml .= '<th style="padding: 10px; text-align: right;">Unit Price</th>';
        $itemsHtml .= '<th style="padding: 10px; text-align: right;">Total</th>';
        $itemsHtml .= '</tr></thead><tbody>';

        foreach ($quote->items as $item) {
            $itemsHtml .= '<tr style="border-bottom: 1px solid #e9ecef;">';
            $itemsHtml .= '<td style="padding: 10px;">' . e($item->description) . '</td>';
            $itemsHtml .= '<td style="padding: 10px; text-align: center;">' . e($item->quantity) . '</td>';
            $itemsHtml .= '<td style="padding: 10px; text-align: right;">' . e($quote->currency . ' ' . number_format($item->unit_price, 2)) . '</td>';
            $itemsHtml .= '<td style="padding: 10px; text-align: right;">' . e($quote->currency . ' ' . number_format($item->total, 2)) . '</td>';
            $itemsHtml .= '</tr>';
        }
        $itemsHtml .= '</tbody></table>';

        $replacements = [
            '{{quote_number}}' => $quote->number,
            '{{quote_title}}' => $quote->title ?: 'Project Proposal',
            '{{client_name}}' => $recipientName,
            '{{client_email}}' => $recipientEmail,
            '{{client_phone}}' => $recipientPhone,
            '{{client_company}}' => $recipientCompany,
            '{{currency}}' => $quote->currency,
            '{{subtotal}}' => $quote->currency . ' ' . number_format($quote->subtotal, 2),
            '{{discount_amount}}' => $quote->currency . ' ' . number_format($quote->discount_amount, 2),
            '{{tax_amount}}' => $quote->currency . ' ' . number_format($quote->tax, 2),
            '{{total_amount}}' => $quote->currency . ' ' . number_format($quote->total, 2),
            '{{valid_until}}' => $quote->valid_until ? $quote->valid_until->format('M d, Y') : 'N/A',
            '{{issue_date}}' => $quote->created_at ? $quote->created_at->format('M d, Y') : date('M d, Y'),
            '{{items_table}}' => $itemsHtml,
            '{{notes}}' => nl2br(e($quote->notes ?? '')),
            '{{terms}}' => nl2br(e($quote->terms ?? '')),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $this->content);
    }

    private static function extractCompany(?string $notes): string
    {
        if ($notes && preg_match('/Company:\s*([^\n]+)/i', $notes, $matches)) {
            return trim($matches[1]);
        }
        return 'Client Organization';
    }
}
