<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GenericAutomationNotice extends Notification
{
    use Queueable;

    public function __construct(public string $title, public string $message, public array $context = []) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event' => $this->context['event'] ?? 'automation',
            'title' => $this->title,
            'message' => $this->message,
            'context' => $this->context,
        ];
    }
}
