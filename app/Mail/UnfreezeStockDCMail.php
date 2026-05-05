<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UnfreezeStockDCMail extends Mailable
{
    use Queueable, SerializesModels;
    private $principal;
    private $mail_body;

    public function __construct($principal, $mail_body)
    {
        $this->principal = $principal;
        $this->mail_body = $mail_body;
    }

    public function build()
    {
        return $this->subject('Pemberitahuan Stock Unfreeze - ' . $this->principal->principal_name)
            ->view('email.UnfreezeStockDC')
            ->with([
                'principal'  => $this->principal,
                'mail_body'  => $this->mail_body,
            ]);
    }
}
