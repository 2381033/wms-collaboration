<?php

namespace App\Console\Commands;

use App\Mail\autoCorrectionEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AutoCorrectionReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Correction:Mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send email auto correction list';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date_from = \Carbon\Carbon::today()->startOfDay();
        $date_to = \Carbon\Carbon::today()->endOfDay();

        $list = DB::table("iv_stock_auto_adjustment_log")
            ->select('id')
            ->whereBetween("correction_date", [$date_from, $date_to])
            ->get();

        if ($list->count() > 0) {
            $this->sendEmail($list);
        }
    }

    private function sendEmail($list)
    {
        $list_to = array('yulio.zulfikar@samudera.id', 'ahmad.zakaria@samudera.id', 'asep.kankan@samudera.id', 'wahyudi.pratama@praweda.id');
        $list_cc = array('irsam.ardiantoro@samudera.id', 'harry.sutrisno@praweda.id', 'kris.akbar@praweda.id');
        $list_bcc = array('wahyudipratamarap@gmail.com');

        $email_to = [];
        for ($i = 0; $i < count($list_to); $i++) {
            if (!empty($list_to[$i]) && $list_to[$i] !== "") {
                $email_to[] = $list_to[$i];
            }
        }

        $email_cc = [];
        for ($i = 0; $i < count($list_cc); $i++) {
            if (!empty($list_cc[$i]) && $list_cc[$i] !== "") {
                $email_cc[] = $list_cc[$i];
            }
        }

        $email_bcc = [];
        for ($i = 0; $i < count($list_bcc); $i++) {
            if (!empty($list_bcc[$i]) && $list_bcc[$i] !== "") {
                $email_bcc[] = $list_bcc[$i];
            }
        }

        Mail::to($email_to)
            ->cc($email_cc)
            ->bcc($email_bcc)
            ->send(new autoCorrectionEmail($list));
    }
}
