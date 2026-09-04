<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Services\Communication\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Campaign $campaign,
        public CampaignRecipient $recipient
    ) {}

    public function handle(EmailService $emailService): void
    {
        $body = str_replace(
            ['{{name}}', '{{email}}'],
            [$this->recipient->recipient_name ?? 'Valued Customer', $this->recipient->recipient_email],
            $this->campaign->content ?? ''
        );

        $sent = $emailService->sendEmail(
            $this->recipient->recipient_email,
            $this->campaign->subject,
            $body
        );

        if ($sent) {
            $this->recipient->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            $this->campaign->increment('successful_count');
        } else {
            $this->recipient->update([
                'status' => 'failed',
                'error_message' => 'Failed to deliver message',
            ]);
            $this->campaign->increment('failed_count');
        }
    }
}