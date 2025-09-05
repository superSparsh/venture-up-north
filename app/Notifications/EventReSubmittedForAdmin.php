<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use App\Models\TermsConditionEvents;

class EventReSubmittedForAdmin extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $event, public $submitter) {}


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
        $statusText = 'resubmitted for review';

        $mail = (new MailMessage)
            ->subject('Event ' . ucfirst($statusText) . ' — Approval Needed')
            ->greeting('Hi Admin,')
            ->line("“{$this->event->name}” was {$statusText} by {$this->submitter->name}.")
            ->line('It is awaiting your review.');


        $terms = TermsConditionEvents::for('contributor_submission');
        $boxes = collect($terms?->boxes ?? [])->where('enabled', true)->values();

        if (!empty($this->event->agreements)) {
            $items = '';

            foreach ($this->event->agreements as $i => $accepted) {
                if (!isset($boxes[$i])) continue;

                $label = $boxes[$i]['label'] ?? "Agreement #{$i}";
                $status = $accepted
                    ? '<span style="color:green;font-weight:600;">Accepted</span>'
                    : '<span style="color:red;font-weight:600;">Not Accepted</span>';

                $items .= "<li style=\"margin:4px 0;font-size:15px\">{$label}: {$status}</li>";
            }

            $html = "<p><strong>Contributor Agreements:</strong></p>
                         <ul style=\"margin:0;padding-left:18px;\">{$items}</ul>";

            $mail->line(new HtmlString($html));
        }

        $mail->action('Review Submission', route('admin.events.review', $this->event->slug))
            ->line('Thanks for keeping the quality high!');

        return $mail;
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
