<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingPendingMail extends Mailable
{
    use Queueable, SerializesModels;
    public $booking;
    public $editLink;
    public function __construct($booking)
    {
        $this->booking = $booking;
        $this->editLink = route('bookings.edit', ['booking_id' => $booking->booking_id]);
    }
    public function build()
    {
        return $this->subject('รออนุมัติการจอง')
                    ->view('emails.bookingPending')
                    ->with([
                        'booking' => $this->booking,
                        'editLink' => $this->editLink,
                    ]);
    }
}
