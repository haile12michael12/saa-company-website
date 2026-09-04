<?php

namespace App\Notifications;

use App\Models\Contract;
use App\Models\ContractSignature;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractSignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Contract $contract,
        public ?ContractSignature $signature = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $signer = $this->signature?->signer_name ?? 'Client';
        return (new MailMessage)
            ->subject("Contract Executed: {$this->contract->number}")
            ->line("Great news! Contract {$this->contract->number} has been digitally signed by {$signer}.")
            ->line("Signed at: " . ($this->signature?->signed_at?->toDayDateTimeString() ?? now()->toDayDateTimeString()))
            ->line("You can view and archive the executed contract in your dashboard.");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Contract {$this->contract->number} Signed",
            'message' => "Contract {$this->contract->number} was signed by " . ($this->signature?->signer_name ?? 'Client'),
            'contract_id' => $this->contract->id,
            'contract_number' => $this->contract->number,
            'signed_at' => $this->signature?->signed_at?->toIso8601String() ?? now()->toIso8601String(),
        ];
    }
}
