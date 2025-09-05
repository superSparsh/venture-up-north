<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventApprovedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Event $event,
        public string $note
    ) {}
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Event Has Been Approved!')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your submission **{$this->event->name}** has been reviewed and approved.")
            ->line("**Information:** {$this->note}")
            ->action('View Your Event', route('event.show', $this->event->slug))
            ->line('Thanks for contributing to Venture Up North!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'title'       => $this->event->name,
            'status'      => 'approved',
        ];
    }
}
