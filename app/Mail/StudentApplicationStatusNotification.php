<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;

class StudentApplicationStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $status;
    public $adminNotes;
    public $requestType;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, $status, $adminNotes = null, $requestType = 'new')
    {
        $this->student = $student;
        $this->status = $status; // 'approved' or 'rejected'
        $this->adminNotes = $adminNotes;
        $this->requestType = $requestType; // 'new' or 'recurring'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->status === 'approved' 
            ? 'Application Request Approved - ILM Mission'
            : 'Application Request Update - ILM Mission';
            
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.student.application-status-notification',
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