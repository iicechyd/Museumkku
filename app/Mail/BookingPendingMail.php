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
    public $cancelLink;
    public $totalPrice;

    public function __construct($booking)
    {
        $this->booking = $booking;
        $this->editLink = route('bookings.edit', ['booking_id' => $booking->booking_id]);
        $this->cancelLink = route('bookings.cancel', ['booking_id' => $booking->booking_id]);

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
        return $this->subject('รออนุมัติการจอง')
            ->view('emails.bookingPending')
            ->with([
                'booking' => $this->booking,
                'editLink' => $this->editLink,
                'cancelLink' => $this->cancelLink,
                'totalPrice' => $this->totalPrice,
            ]);
    }
}
