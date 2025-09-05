<?php

namespace App\Notifications;

use App\Models\TermsCondition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class MagazineSubmittedForAdmin extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $magazine, public $submitter) {}


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
        $mail = (new MailMessage)
            ->subject('New Magazine Submission — Approval Needed')
            ->greeting('Hi Admin,')
            ->line("“{$this->magazine->title}” was submitted by {$this->submitter->name}.")
            ->line('It is awaiting your review.');


        $terms = TermsCondition::for('contributor_submission');
        $boxes = collect($terms?->boxes ?? [])->where('enabled', true)->values();

        if (!empty($this->magazine->agreements)) {
            $items = '';

            foreach ($this->magazine->agreements as $i => $accepted) {
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

        $mail->action('Review Submission', route('admin.magazines.review', $this->magazine->slug))
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
