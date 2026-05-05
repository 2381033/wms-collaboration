<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WHchartMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $fileurl;

    public function __construct($fileurl)
    {
        $this->fileurl = $fileurl;
    }

    public function build()
    {
        return $this->markdown('emails.warehouse.trx_chat')->subject('Dashboard Transaction')->attach($this->fileurl);
    }
}