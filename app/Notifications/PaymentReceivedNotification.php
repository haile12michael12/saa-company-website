<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(public mixed $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment received')
            ->line('A payment has been received.')
            ->line('Amount: '.($this->value('amount') ?? 'Not provided'))
            ->line('Reference: '.($this->value('reference', 'transaction_id') ?? 'Not provided'));
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Payment received',
            'message' => 'A payment has been received.',
            'payment_id' => $this->value('id'),
            'amount' => $this->value('amount'),
            'reference' => $this->value('reference', 'transaction_id'),
        ];
    }

    private function value(string $key, ?string $fallback = null): mixed
    {
        return data_get($this->payment, $key) ?? ($fallback ? data_get($this->payment, $fallback) : null);
    }
}