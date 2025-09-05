<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VentureSubmittedForAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $venture)
    {
        //
    }

    public function via(object $notifiable): array
    {
        // Add 'database' if you also want an in-app bell notification
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $venture   = $this->venture;
        $title     = $venture->title ?? 'Untitled Venture';
        $submittedAt = optional($venture->created_at)->timezone(config('app.timezone'))?->format('M d, Y g:i A');
        $submitterName  = $venture->user->name ?? ($venture->submitted_name ?? 'Guest');
        $submitterEmail = $venture->user->email ?? ($venture->submitted_email ?? null);

        // Prefer a named route if you have one; fallback to a sensible URL
        $reviewUrl = rescue(
            fn() => route('admin.ventures.edit', $venture->id),
            url("/admin/ventures/{$venture->id}/edit")
        );

        $msg = (new MailMessage)
            ->subject('[Action Required] Venture Submitted for Review')
            ->greeting('Venture Submission')
            ->line("**{$title}** has just been submitted and is awaiting your review.")
            ->line('**Submitted by:** ' . ($submitterEmail ? "{$submitterName} ({$submitterEmail})" : $submitterName));

        if ($submittedAt) {
            $msg->line('**Submitted at:** ' . $submittedAt);
        }

        return $msg
            ->action('Review Venture', $reviewUrl);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'venture_id' => $this->venture->id ?? null,
            'title'      => $this->venture->title ?? null,
            'submitted_by' => $this->venture->user->name
                ?? ($this->venture->submitted_name ?? 'Guest'),
        ];
    }
}
