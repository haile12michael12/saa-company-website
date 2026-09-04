<?php

namespace App\Services\Marketing;

use App\Jobs\SendCampaignEmail;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CampaignService
{
    public function getCampaignsForCompany(?int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Campaign::with(['creator'])->withCount(['recipients']);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('subject', 'like', "%{$filters['search']}%");
            });
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function createCampaign(array $data, ?int $companyId = null, ?User $creator = null): Campaign
    {
        $companyId = $companyId ?? ($creator?->company_id ?? null);

        return DB::transaction(function () use ($data, $companyId, $creator) {
            $campaign = Campaign::create([
                'company_id' => $companyId,
                'name' => $data['name'],
                'subject' => $data['subject'],
                'type' => $data['type'] ?? 'email',
                'status' => 'draft',
                'content' => $data['content'] ?? '',
                'target_audience' => $data['target_audience'] ?? ['type' => 'all_customers'],
                'scheduled_at' => !empty($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null,
                'created_by' => $creator?->id,
            ]);

            $this->populateRecipients($campaign);

            return $campaign;
        });
    }

    public function populateRecipients(Campaign $campaign): int
    {
        $target = $campaign->target_audience ?? ['type' => 'all_customers'];
        $type = is_array($target) ? ($target['type'] ?? 'all_customers') : 'all_customers';

        $recipients = [];

        if ($type === 'all_customers' || $type === 'all') {
            $customers = Customer::where('company_id', $campaign->company_id)
                ->whereNotNull('email')
                ->get(['id', 'name', 'email']);

            foreach ($customers as $c) {
                $recipients[$c->email] = [
                    'campaign_id' => $campaign->id,
                    'recipient_email' => $c->email,
                    'recipient_name' => $c->name,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if ($type === 'active_leads' || $type === 'all') {
            $leads = Lead::where('company_id', $campaign->company_id)
                ->whereNotNull('email')
                ->get(['id', 'name', 'email']);

            foreach ($leads as $l) {
                if (!isset($recipients[$l->email])) {
                    $recipients[$l->email] = [
                        'campaign_id' => $campaign->id,
                        'recipient_email' => $l->email,
                        'recipient_name' => $l->name,
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (!empty($recipients)) {
            CampaignRecipient::insert(array_values($recipients));
        }

        $total = count($recipients);
        $campaign->update(['total_recipients' => $total]);

        return $total;
    }

    public function dispatchCampaign(Campaign $campaign): Campaign
    {
        $campaign->update([
            'status' => 'sending',
            'sent_at' => now(),
        ]);

        $recipients = $campaign->recipients()->where('status', 'pending')->get();

        foreach ($recipients as $recipient) {
            SendCampaignEmail::dispatch($campaign, $recipient);
        }

        $campaign->update(['status' => 'sent']);

        return $campaign;
    }

    public function trackOpen(CampaignRecipient $recipient): void
    {
        if ($recipient->status !== 'opened' && $recipient->status !== 'clicked') {
            $recipient->update([
                'status' => 'opened',
                'opened_at' => now(),
            ]);

            $recipient->campaign->increment('opened_count');
        }
    }

    public function trackClick(CampaignRecipient $recipient): void
    {
        $recipient->update([
            'status' => 'clicked',
            'clicked_at' => now(),
        ]);

        $recipient->campaign->increment('clicked_count');
    }
}
