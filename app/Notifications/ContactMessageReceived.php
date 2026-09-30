<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContactMessageReceived extends Notification
{
    use Queueable;

    public function __construct(public ContactMessage $contact) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pesan kontak baru: '.($this->contact->subject ?: 'Tanpa subjek'),
            'body' => 'Dari '.$this->contact->name.' ('.$this->contact->email.')',
            'url' => '/admin/contact-messages/'.$this->contact->id,
            'contact_message_id' => $this->contact->id,
        ];
    }
}
