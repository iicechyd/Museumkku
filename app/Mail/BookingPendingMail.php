<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class BookingPendingMail extends Mailable
{
    use Queueable, SerializesModels;
    public $booking;
    public $detailsLink;
    public $editLink;
    public $cancelLink;
    public $totalPrice;

    public function __construct($booking)
    {
        $this->booking = $booking;
        $this->detailsLink = URL::signedRoute('bookings.details', ['booking_id' => $booking->booking_id]);
        $this->editLink = URL::signedRoute('bookings.edit', ['booking_id' => $booking->booking_id]);
        $this->cancelLink = URL::signedRoute('bookings.cancel', ['booking_id' => $booking->booking_id]);

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
        return $this->subject('รออนุมัติการจอง')
            ->view('emails.bookingPending')
            ->with([
                'booking' => $this->booking,
                'detailsLink' => $this->detailsLink,
                'editLink' => $this->editLink,
                'cancelLink' => $this->cancelLink,
                'totalPrice' => $this->totalPrice,
            ]);
    }
}
