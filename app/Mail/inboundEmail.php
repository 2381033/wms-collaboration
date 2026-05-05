<?php

namespace App\Mail;

use App\Exports\InboundEmailExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class inboundEmail extends Mailable
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
            $files[] = "inbound_$value->job_no.xlsx";
            
            Excel::store(new InboundEmailExport($value->id), "inbound_$value->job_no.xlsx");            
        }

        $message = $this->markdown('email/inboundEmail')->subject('Inbound Transaction')->with("data", $this->message);  
        
        foreach ($files as $file) { 
            $message->attach(storage_path("app/".$file));
        }

        return $message;
    }
}
