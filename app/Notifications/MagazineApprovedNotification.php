<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\VentureMagazine;

class MagazineApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public VentureMagazine $magazine,
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
            ->subject('Your Magazine Has Been Approved!')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your submission **{$this->magazine->title}** has been reviewed and approved.")
            ->line("**Information:** {$this->note}")
            ->action('View Your Magazine', route('show.magazine', $this->magazine->slug))
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
            'magazine_id' => $this->magazine->id,
            'title'       => $this->magazine->title,
            'status'      => 'approved',
        ];
    }
}
