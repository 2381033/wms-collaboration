<?php

namespace App\Mail;

use App\Exports\AutoCorrectionEmailExport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class autoCorrectionEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $principal;
    private $branch;
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function build()
    {
        $files = [];
        $datefile = date('Y-m-d');
        $files[] = "autoCorrection_$datefile.xlsx";
        // dd($this->message);
        // foreach ($this->message as $value) {
            if (isset($this->message)) {
                Excel::store(new AutoCorrectionEmailExport(1), "autoCorrection_$datefile.xlsx");
            }
        // }

        $message = $this->markdown('email/autoCorrectionEmail')->subject('AutoCorrection Transaction')->with("data", $this->message);

        foreach ($files as $file) {
            $message->attach(storage_path("app/".$file));
        }

        return $message;
    }
}
