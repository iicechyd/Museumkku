<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class BookingApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $detailsLink;
    public $uploadLink;
    public $totalPrice;
    public $cancelLink;



    public function __construct($booking, $uploadLink)
    {
        $this->booking = $booking;
        $this->detailsLink = URL::signedRoute('bookings.details', ['booking_id' => $booking->booking_id]);
        $this->uploadLink = $uploadLink;
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
        return $this->subject('การจองของคุณได้รับการอนุมัติแล้ว')
                    ->view('emails.bookingApproved')
                    ->with([
                        'booking' => $this->booking,
                        'detailsLink' => $this->detailsLink,
                        'uploadLink' => $this->uploadLink,
                        'cancelLink' => $this->cancelLink,
                        'totalPrice' => $this->totalPrice,
                    ]);
    }
}
