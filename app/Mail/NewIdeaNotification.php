<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewIdeaNotification extends Mailable
{

    use Queueable, SerializesModels;
    public $idea;
    /**
     * Create a new message instance.
     */
    public function __construct($idea)
    {
        $this->idea = $idea;
    }
    public function build()
{
    return $this->subject('New Idea Submitted in your Department')
                ->view('emails.new_idea'); // Bạn cần tạo file view này
}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Idea Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
