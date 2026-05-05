<?php

namespace App\Console\Commands;

use App\Exports\InboundExport;
use App\Mail\inboundEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Transaction\EmailPrincipal as TransactionEmailPrincipal;

class InboundReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inbound:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notification to user about inbound process.';

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

        $principal_list = DB::table("iv_principal as a")
            ->join("iv_principal_branch as b", "a.id", "b.principal_id")
            ->where("active", "Yes")
            ->orderBy("a.id", "asc")
            ->orderBy("b.branch_id", "asc");
        $hanyahempelsurabaya = false;
        if ($hanyahempelsurabaya) {
            $principal_list->where("short_name", "Hempel");
        }
        $principal_list = $principal_list->get();

        foreach ($principal_list as $value) {
            $list = DB::table("iv_inbound_job as a")
                ->select("a.*")
                ->where("a.branch_id", $value->branch_id)
                ->where("a.principal_id", $value->principal_id)
                ->whereBetween("a.confirmed_date", [$date_from, $date_to])
                ->where("a.confirmed_flag", "Yes")
                ->get();

            if ($list->count() > 0) {
                if ($hanyahempelsurabaya) {
                    $this->sendEmail($value->principal_id, 3, $list);
                } else {
                    $this->sendEmail($value->principal_id, $value->branch_id, $list);
                }
            }
        }

        return 0;
    }

    private function sendEmail($principal_id, $branch_id, $list)
    {
        $sendData = TransactionEmailPrincipal::where("branch_id", $branch_id)
            ->where("principal_id", $principal_id)
            ->where("description", "Inbound Transaction")
            ->first();

        if (isset($sendData)) {
            $list_to = explode(";", $sendData->email_to);
            $list_cc = explode(";", $sendData->email_cc);
            $list_bcc = explode(";", $sendData->email_bcc);

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
                ->send(new inboundEmail($principal_id, $branch_id, $list));

            foreach ($list as $value) {
                $filename = "inbound_$value->job_no.xlsx";
                Storage::delete($filename);
            }
        }
    }
}
