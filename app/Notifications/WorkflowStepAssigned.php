<?php

namespace App\Notifications;

use App\Models\Document;
use App\Models\WorkflowStep;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkflowStepAssigned extends Notification
{
    use Queueable;

    public function __construct(public Document $document, public WorkflowStep $step)
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $doc = $this->document;
        $step = $this->step;
        $subject = sprintf('Action required: %s — %s', $doc->doc_number ?: $doc->id, $doc->title);
        $line1 = sprintf('You have been assigned to the "%s" step for document "%s".', $step->name, $doc->title);

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello')
            ->line($line1)
            ->line('Open the document to review and take action (Approve/Reject if applicable).')
            ->action('Open Document', route('documents.show', $doc))
            ->line('If you believe you received this in error, please contact your administrator.');
    }
}
