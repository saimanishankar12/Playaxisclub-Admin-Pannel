<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlayerOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $playerName;
    public string $otp;

    public function __construct(string $playerName, string $otp)
    {
        $this->playerName = $playerName;
        $this->otp        = $otp;
    }

    public function build()
    {
        return $this->subject('Your Login OTP - Playaxisclub')
                    ->view('emails.player_otp');
    }
}