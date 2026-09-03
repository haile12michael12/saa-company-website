<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceOverdueNotification extends Notification
{
    use Queueable;

    public function __construct(public mixed $invoice)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invoice overdue')
            ->line('An invoice is overdue.')
            ->line('Invoice: '.($this->value('invoice_number', 'number') ?? 'Not provided'))
            ->line('Due date: '.($this->value('due_date') ?? 'Not provided'))
            ->line('Amount: '.($this->value('amount') ?? 'Not provided'));
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Invoice overdue',
            'message' => 'An invoice is overdue.',
            'invoice_id' => $this->value('id'),
            'invoice_number' => $this->value('invoice_number', 'number'),
            'due_date' => $this->value('due_date'),
            'amount' => $this->value('amount'),
        ];
    }

    private function value(string $key, ?string $fallback = null): mixed
    {
        return data_get($this->invoice, $key) ?? ($fallback ? data_get($this->invoice, $fallback) : null);
    }
}