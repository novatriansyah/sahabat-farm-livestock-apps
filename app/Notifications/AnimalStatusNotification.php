<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnimalStatusNotification extends Notification
{
    use Queueable;

    protected $animal;
    protected $message;
    protected $type;
    protected $customUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($animal, $message, $type = 'info', $customUrl = null)
    {
        $this->animal = $animal;
        $this->message = $message;
        $this->type = $type;
        $this->customUrl = $customUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'Notifikasi Ternak: ' . ($this->animal->tag_id ?? 'Sistem');
        $url = $this->customUrl ?? ($this->animal ? route('animals.show', $this->animal->id) : route('dashboard'));

        return (new MailMessage)
            ->subject($subject)
            ->line($this->message)
            ->action('Lihat Detail', $url)
            ->line('Terima kasih telah menggunakan Sahabat Farm Indonesia!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'animal_id' => $this->animal->id ?? null,
            'tag_id' => $this->animal->tag_id ?? 'Sistem',
            'message' => $this->message,
            'type' => $this->type,
            'url' => $this->customUrl ?? ($this->animal ? route('animals.show', $this->animal->id) : route('dashboard')),
        ];
    }
}
