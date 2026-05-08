<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;
use App\Models\StudentApplication;

class AdminApplicationRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $application;
    public $requestType;

    /**
     * Create a new message instance.
     */
    public function __construct(Student $student, StudentApplication $application = null, $requestType = 'new')
    {
        $this->student = $student;
        $this->application = $application;
        $this->requestType = $requestType; // 'new' or 'recurring'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->requestType === 'recurring' 
            ? 'New Recurring Application Request' 
            : 'New Application Submission Request';
            
        // Add application number to subject for recurring applications
        if ($this->requestType === 'recurring' && $this->application) {
            $subject .= ' (App #' . $this->application->application_number . ')';
        }
            
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
            markdown: 'emails.admin-application-request',
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
