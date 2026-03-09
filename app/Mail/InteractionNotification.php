<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InteractionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $idea;
    public $sender;
    public $actionType; // 'commented on' hoặc 'liked'
    public $commentContent;

    public function __construct($idea, $sender, $actionType, $commentContent = null)
    {
        $this->idea = $idea;
        $this->sender = $sender;
        $this->actionType = $actionType;
        $this->commentContent = $commentContent;
    }

    public function build()
    {
        $subject = "[UIMS] {$this->sender->full_name} has {$this->actionType} your idea";

        return $this->subject($subject)
                    ->view('emails.interaction_notification');
    }
}
