<?php

namespace App\Mail;

use App\Exports\LedgerVTransactionEmailExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class LedgerVTransactionEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function build()
    {
        Excel::store(new LedgerVTransactionEmailExport($this->message), "LVTini.xlsx");
        // dd($this->message);
        $message = $this->markdown('email/LedgerVTransactionEmail')->subject('Inbound Transaction')->with("data", $this->message);
        $message->attach(storage_path("app/LVTini.xlsx"));

        // foreach ($files as $file) {
        //     $message->attach(storage_path("app/".$file));
        // }

        return $message;
    }
}
