<?php

namespace App\Services\Contracts;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContractService
{
    public function getContractsForCompany(?int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Contract::with(['customer', 'quote', 'signatures']);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('number', 'like', "%{$filters['search']}%")
                  ->orWhere('content', 'like', "%{$filters['search']}%");
            });
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function createContract(array $data, ?int $companyId = null, ?User $creator = null): Contract
    {
        $companyId = $companyId ?? ($creator?->company_id ?? null);

        return DB::transaction(function () use ($data, $companyId) {
            return Contract::create([
                'company_id' => $companyId,
                'customer_id' => $data['customer_id'] ?? null,
                'quote_id' => $data['quote_id'] ?? null,
                'number' => $data['number'] ?? ('CTR-' . strtoupper(Str::random(8))),
                'status' => $data['status'] ?? 'draft',
                'content' => $data['content'] ?? '',
                'starts_at' => $data['starts_at'] ?? now()->toDateString(),
                'ends_at' => $data['ends_at'] ?? null,
            ]);
        });
    }

    public function generateFromQuote(Quote $quote, array $options = []): Contract
    {
        $content = "AGREEMENT FOR SERVICES\n\n";
        $content .= "This Service Contract is entered into between " . ($quote->company->name ?? 'Service Provider') . " and " . ($quote->customer->name ?? $quote->name) . ".\n\n";
        $content .= "1. Scope of Work:\n" . ($quote->notes ?? 'Professional services as detailed in Quote #' . $quote->quote_number) . "\n\n";
        $content .= "2. Financial Consideration:\nTotal Contract Amount: $" . number_format($quote->total ?? $quote->total_amount ?? 0, 2) . "\n\n";
        $content .= "3. Term:\nEffective Date: " . now()->format('Y-m-d') . "\n";
        if (!empty($options['duration_months'])) {
            $content .= "Contract Duration: " . $options['duration_months'] . " months.\n";
        }
        $content .= "\n4. Terms & Conditions:\nStandard service terms and warranties apply. All deliverables remain proprietary until full invoice settlement.";

        return $this->createContract([
            'customer_id' => $quote->customer_id,
            'quote_id' => $quote->id,
            'number' => 'CTR-' . ($quote->quote_number ?? strtoupper(Str::random(8))),
            'content' => $content,
            'starts_at' => now()->toDateString(),
            'ends_at' => !empty($options['duration_months']) ? now()->addMonths((int)$options['duration_months'])->toDateString() : null,
            'status' => 'draft',
        ], $quote->company_id);
    }

    public function updateContract(Contract $contract, array $data): Contract
    {
        $contract->update($data);
        return $contract;
    }

    public function sendContract(Contract $contract): Contract
    {
        $contract->update(['status' => 'sent']);
        return $contract;
    }

    public function voidContract(Contract $contract, ?string $reason = null): Contract
    {
        $contract->update([
            'status' => 'cancelled',
            'content' => $reason ? ($contract->content . "\n\n[VOIDED: " . $reason . "]") : $contract->content,
        ]);

        return $contract;
    }
}