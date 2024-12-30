<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $uploadLink;

    public function __construct($booking, $uploadLink)
    {
        $this->booking = $booking;
        $this->uploadLink = $uploadLink;
    }

    public function build()
    {
        return $this->subject('การจองของคุณได้รับการอนุมัติแล้ว')
                    ->view('emails.bookingApproved')
                    ->with([
                        'booking' => $this->booking,
                        'uploadLink' => $this->uploadLink,
                    ]);
    }
}
