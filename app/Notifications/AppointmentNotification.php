<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentNotification extends Notification
{
    use Queueable;

    public function __construct(public mixed $appointment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Appointment notification')
            ->line('An appointment requires your attention.')
            ->line('Client: '.($this->value('client_name', 'name') ?? 'Not provided'))
            ->line('Scheduled for: '.($this->value('scheduled_at', 'appointment_date') ?? 'Not provided'));
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Appointment notification',
            'message' => 'An appointment requires your attention.',
            'appointment_id' => $this->value('id'),
            'client_name' => $this->value('client_name', 'name'),
            'scheduled_at' => $this->value('scheduled_at', 'appointment_date'),
        ];
    }

    private function value(string $key, ?string $fallback = null): mixed
    {
        return data_get($this->appointment, $key) ?? ($fallback ? data_get($this->appointment, $fallback) : null);
    }
}