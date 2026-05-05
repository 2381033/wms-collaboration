<?php

namespace App\Mail;

use App\Exports\LedgerExportMailExcel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class LedgerExport extends Mailable
{
    use Queueable, SerializesModels;
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $file = "Stock-Ledger-Export-" . date('d-m-Y')  . ".xlsx";
        Excel::store(new LedgerExportMailExcel($this->data), $file);
        $message = $this->markdown('email/LedgerExportEmail');
        $message->subject("Stock Ledger Export");
        $message->attach(storage_path("app/" . $file));
        return $message;
    }
}
