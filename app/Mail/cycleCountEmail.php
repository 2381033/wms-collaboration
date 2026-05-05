<?php

namespace App\Mail;

use App\Exports\cycleCountEmailExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Master\Principal as MasterPrincipal;

class cycleCountEmail extends Mailable
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
        $principal = MasterPrincipal::find($this->principal);
        $file = "cycleCount_$principal->short_name.xlsx";
        // dd($this->principal,$this->branch, $file);
        Excel::store(new cycleCountEmailExport($this->principal, $this->branch), "cycleCount_$principal->short_name.xlsx");

        $message = $this->markdown('email/cycleCountEmail');
        $message->subject("Cycle Count $principal->principal_name");
        $message->attach(storage_path("app/" . $file));

        return $message;
    }
}
