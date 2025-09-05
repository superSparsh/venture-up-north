<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\VentureMagazine;

class MagazineRejected extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public VentureMagazine $magazine,
        public string $reason
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
            ->subject('Your Magazine Submission Was Rejected')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your submission **{$this->magazine->title}** has been rejected.")
            ->line("**Reason:** {$this->reason}")
            ->line('You may update your submission and resubmit for review.')
            ->action('Edit Your Magazine', route('contributor.magazines.edit', $this->magazine->id))
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
            //
        ];
    }
}
