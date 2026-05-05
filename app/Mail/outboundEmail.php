<?php

namespace App\Mail;

use App\Exports\OutboundEmailExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class outboundEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $principal;
    private $branch;
    private $message;

    public function __construct($principal, $branch, $message)
    {
        $this->branch = $branch;
        $this->principal = $principal;
        $this->message = $message;
    }

    public function build()
    {
        $files = [];
        foreach ($this->message as $value) {
            $files[] = "outbound_$value->job_no.xlsx";

            Excel::store(new OutboundEmailExport($value->id), "outbound_$value->job_no.xlsx");
        }

        $message = $this->view('email/outboundEmail')->subject('Outbound Transaction')->with("data", $this->message);

        foreach ($files as $file) {
            $message->attach(storage_path("app/" . $file));
        }

        return $message;
    }
}
