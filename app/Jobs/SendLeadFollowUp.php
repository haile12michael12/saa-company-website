<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Services\Communication\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendLeadFollowUp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public string $stage = 'initial_intro'
    ) {}

    public function handle(EmailService $emailService): void
    {
        if (empty($this->lead->email)) {
            return;
        }

        $subject = "Following up on your project inquiry - " . ($this->lead->name ?? 'Hello');
        $body = "Hi {$this->lead->name},\n\nThank you for reaching out to us. We wanted to follow up and see if you had any questions regarding our solutions or if you would like to schedule a brief consultation call.\n\nBest regards,\nThe Team";

        $emailService->sendEmail($this->lead->email, $subject, $body);
    }
}