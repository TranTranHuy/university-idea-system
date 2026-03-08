<?php

namespace App\Mail;

use App\Models\Idea;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewIdeaNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $idea; // Biến này sẽ chứa dữ liệu ý tưởng để in ra mail

    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Idea Submitted: ' . $this->idea->title, // Tiêu đề Email
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new_idea', // Trỏ chính xác đến file giao diện mail
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
