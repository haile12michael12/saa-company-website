<?php

namespace App\Services\Communication;

use App\Jobs\SendLeadFollowUp;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendEmail(string $to, string $subject, string $body, array $options = []): bool
    {
        try {
            Mail::raw($body, function ($message) use ($to, $subject, $options) {
                $message->to($to)
                    ->subject($subject);

                if (!empty($options['cc'])) {
                    $message->cc($options['cc']);
                }
                if (!empty($options['bcc'])) {
                    $message->bcc($options['bcc']);
                }
            });

            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send email to {$to}: " . $e->getMessage());
            return false;
        }
    }

    public function queueLeadFollowUp(Lead $lead, string $followUpStage = 'initial_intro', int $delayMinutes = 60): void
    {
        SendLeadFollowUp::dispatch($lead, $followUpStage)->delay(now()->addMinutes($delayMinutes));
    }
}