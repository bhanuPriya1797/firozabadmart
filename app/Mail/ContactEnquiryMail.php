<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $enquiry;
    public $websiteName;
    public $logoUrl;

    public function __construct($enquiry, $websiteName, $logoUrl)
    {
        $this->enquiry = $enquiry;
        $this->websiteName = $websiteName;
        $this->logoUrl = $logoUrl;
    }

    public function build()
    {
        return $this->subject('New Contact Enquiry - ' . ($this->enquiry->name ?? ''))
            ->view('emails.contact-enquiry')
            ->with([
                'enquiry' => $this->enquiry,
                'websiteName' => $this->websiteName,
                'logoUrl' => $this->logoUrl,
            ]);
    }
}

