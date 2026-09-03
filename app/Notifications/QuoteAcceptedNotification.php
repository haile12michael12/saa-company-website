<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(public mixed $quote)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Quote accepted')
            ->line('A quote has been accepted.')
            ->line('Quote: '.($this->value('quote_number', 'number') ?? 'Not provided'))
            ->line('Client: '.($this->value('client_name', 'name') ?? 'Not provided'));
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Quote accepted',
            'message' => 'A quote has been accepted.',
            'quote_id' => $this->value('id'),
            'quote_number' => $this->value('quote_number', 'number'),
            'client_name' => $this->value('client_name', 'name'),
        ];
    }

    private function value(string $key, ?string $fallback = null): mixed
    {
        return data_get($this->quote, $key) ?? ($fallback ? data_get($this->quote, $fallback) : null);
    }
}