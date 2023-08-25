<?php

namespace App\Mail\Customer;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpSent extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $otp;
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->from('otp@jetpax.in')
            ->subject('OTP verification | '.config('app.name'))->markdown('emails.customer.otp_sent');
    }
}
