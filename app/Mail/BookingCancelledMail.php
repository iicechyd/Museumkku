<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $totalPrice;

    public function __construct($booking)
    {
        $this->booking = $booking;

        $childrenPrice = $booking->children_qty * ($booking->activity->children_price ?? 0);
        $studentPrice = $booking->students_qty * ($booking->activity->student_price ?? 0);
        $adultPrice = $booking->adults_qty * ($booking->activity->adult_price ?? 0);
        $disabledPrice = $booking->disabled_qty * ($booking->activity->disabled_price ?? 0);
        $elderlyPrice = $booking->elderly_qty * ($booking->activity->elderly_price ?? 0);
        $monkPrice = $booking->monk_qty * ($booking->activity->monk_price ?? 0);
        
        $this->totalPrice = $childrenPrice + $studentPrice + $adultPrice + $disabledPrice + $elderlyPrice + $monkPrice;

    }
    public function build()
    {
        return $this->subject('การจองของคุณถูกยกเลิก')
                    ->view('emails.bookingCancelled')
                    ->with([
                        'booking' => $this->booking,
                        'totalPrice' => $this->totalPrice,

                    ]);
    }
}
