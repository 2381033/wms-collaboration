<?php

namespace App\Console\Commands;

use App\Mail\cycleCountEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use App\Models\Transaction\EmailPrincipal as TransactionEmailPrincipal;
// use App\Models\Master\Principal as MasterPrincipal;

class CycleCountReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cycleCount:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notification to user about cycle count.';

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
        $principal_list = TransactionEmailPrincipal::where("description", "Cycle Count")->get();

        foreach ($principal_list as $value) {
            $date_from = date('Y-m-d', strtotime('-7 days'));
            $date_to   = date('Y-m-d', strtotime('-1 days'));
            $cycle_count = DB::table("iv_cyclecount_detail as icd")
                ->select('id')
                ->where("icd.principal_id", $value->principal_id)
                ->where("icd.branch_id", $value->branch_id)
                ->whereBetween("icd.created_at", [$date_from, $date_to])
                ->count();
                // dd($cycle_count,$date_from,$date_to);
            // $cycle_count_sql = $cycle_count->toSql();
            // $cycle_count_bindings = $cycle_count->getBindings();
            if ($cycle_count > 0) {
                // $this->sendEmail($value->principal_id, $value->branch_id);
                // dd($cycle_count);
                $list_to = explode(";", $value->email_to);
                $list_cc = explode(";", $value->email_cc);
                $list_bcc = explode(";", $value->email_bcc);

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
                // $email = new cycleCountEmail($value->principal_id, $value->branch_id);

                Mail::to($email_to)
                    ->cc($email_cc)
                    ->bcc($email_bcc)
                    ->send(new cycleCountEmail($value->principal_id, $value->branch_id));

                // $filename = $value->short_name . "_cycle_count.xlsx";
                // dd($email);
                // Storage::delete($filename);
            } else {
                echo "Tidak ada data Cycle Count untuk periode $date_from - $date_to";
            }
        }

        return 0;
    }
}
