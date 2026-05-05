<?php

namespace App\Mail;

use App\Exports\StockEmailExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class stockEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $branch;
    private $principal;

    public function __construct($principal, $branch)
    {
        $this->principal = $principal;
        $this->branch = $branch;
    }

    public function build()
    {
        $principal = \app\Models\Master\Principal::find($this->principal);
        $file = "stockledger_$principal->short_name.xlsx";

        Excel::store(new StockEmailExport($this->principal, $this->branch), "stockledger_$principal->short_name.xlsx");

        $message = $this->markdown('email/stockEmail');
        $message->subject("Stock Ledger $principal->principal_name");
        $message->attach(storage_path("app/".$file));

        return $message;
    }
}
