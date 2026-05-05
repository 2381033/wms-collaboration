<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $fileurl;

    public function __construct($fileurl)
    {
        $this->fileurl = $fileurl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.booking')->subject('Booking CY Handling')->attach($this->fileurl);
    }
}
