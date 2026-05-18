<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $details;

    /**
     * $details keys:
     *  - type              : 'singles' | 'doubles'
     *  - player_name       : string  (Player 1 name, used in greeting)
     *  - player_email      : string  (Player 1 email — primary recipient)
     *  - player_id         : string  e.g. PLY0001  (singles only)
     *  - player1_id        : string  (doubles only)
     *  - player2_id        : string  (doubles only)
     *  - season_id         : string  e.g. ALK0001S
     *  - amount            : int     amount in rupees
     *  - razorpay_payment_id : string
     *  - razorpay_order_id   : string
     *  - payment_date      : string  formatted date
     */
    public function __construct(array $details)
    {
        $this->details = $details;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ' Registration Confirmed Ekalavya Badminton Tournament',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration_confirmation',
            with: ['details' => $this->details],
        );
    }
}