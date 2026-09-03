<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLeadNotification extends Notification
{
    use Queueable;

    public function __construct(public mixed $lead)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New lead received')
            ->line('A new lead has been submitted.')
            ->line('Name: '.($this->value('name') ?? 'Unknown'))
            ->line('Email: '.($this->value('email') ?? 'Not provided'))
            ->line('Subject: '.($this->value('subject') ?? 'Not provided'));
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New lead received',
            'message' => 'A new lead has been submitted.',
            'name' => $this->value('name'),
            'email' => $this->value('email'),
            'subject' => $this->value('subject'),
        ];
    }

    private function value(string $key): mixed
    {
        return data_get($this->lead, $key);
    }
}