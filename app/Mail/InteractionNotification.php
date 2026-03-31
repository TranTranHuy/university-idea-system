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
    public $actionType; // 'commented on', 'liked', 'disliked'
    public $content;

    public function __construct($idea, $sender, $actionType, $content = null)
    {
        $this->idea = $idea;
        $this->sender = $sender;
        $this->actionType = $actionType;
        $this->content = $content;
    }

    public function build()
    {
        return $this->subject("New interaction on your idea: " . $this->idea->title)
                    ->view('emails.interaction_notification');
    }
}
