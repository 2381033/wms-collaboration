<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FreezeStockDCMail extends Mailable
{
    use Queueable, SerializesModels;
    private $principal;
    private $body_email;
    private $activity;

    public function __construct($principal, $body_email, $activity)
    {
        $this->principal = $principal;
        $this->body_email = $body_email;
        $this->activity = $activity;
    }

    public function build()
    {
        return $this->subject('Peringatan Freeze Activity ' . $this->activity . '.  - ' . $this->principal->principal_name)
            ->view('email.FreezeStockDC')
            ->with([
                'principal'  => $this->principal,
                'body_email' => $this->body_email,
                'activity'  => $this->activity
            ]);
    }
}
