<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class BookingCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $detailsLink;
    public $totalPrice;

    public function __construct($booking)
    {
        $this->booking = $booking;
        $this->detailsLink = URL::signedRoute('bookings.details', ['booking_id' => $booking->booking_id]);

        $childrenPrice = $booking->children_qty * ($booking->activity->children_price ?? 0);
        $studentPrice = $booking->students_qty * ($booking->activity->student_price ?? 0);
        $adultPrice = $booking->adults_qty * ($booking->activity->adult_price ?? 0);
        $kidPrice = $booking->kid_qty * ($booking->activity->kid_price ?? 0);
        $disabledPrice = $booking->disabled_qty * ($booking->activity->disabled_price ?? 0);
        $elderlyPrice = $booking->elderly_qty * ($booking->activity->elderly_price ?? 0);
        $monkPrice = $booking->monk_qty * ($booking->activity->monk_price ?? 0);
        
        $this->totalPrice = $childrenPrice + $studentPrice + $adultPrice + $kidPrice + $disabledPrice + $elderlyPrice + $monkPrice;

    }
    public function build()
    {
        return $this->subject('การจองของคุณถูกยกเลิก')
                    ->view('emails.bookingCancelled')
                    ->with([
                        'booking' => $this->booking,
                        'detailsLink' => $this->detailsLink,
                        'totalPrice' => $this->totalPrice,
                    ])
                    ->attach(public_path('img/cancel_icon.png'), [
                        'as' => 'cancel_icon.png',
                        'mime' => 'image/png',
                        'inline' => true,
                    ])
                    ->attach(public_path('img/phone_icon.png'), [
                        'as' => 'phone_icon.png',
                        'mime' => 'image/png',
                        'inline' => true,
                    ]);
    }
}
