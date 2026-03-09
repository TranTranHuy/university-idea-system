<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Idea;

class StaffIdeaSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $idea;

    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function build()
    {
        return $this->subject('[UIMS] Action Required: New Idea Submitted in Your Department')
                    ->view('emails.staff_submission');
    }
}
