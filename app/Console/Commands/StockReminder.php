<?php

namespace App\Console\Commands;

use App\Mail\stockEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use App\Models\Transaction\EmailPrincipal as TransactionEmailPrincipal;
use App\Models\Master\Principal as MasterPrincipal;

class StockReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notification to user about stock ledger.';

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
        $principal_list = DB::table("iv_principal as a")
                                    ->join("iv_principal_branch as b", "a.id", "b.principal_id")
                                    ->where("active", "Yes")
                                    ->orderBy("a.id", "asc")
                                    ->orderBy("b.branch_id", "asc");
        $hanyahempelsurabaya = true;
        if ($hanyahempelsurabaya) {
                $principal_list->whereIn("short_name", ["Hempel","SHAD"]);
        }
        $principal_list = $principal_list->get();

        foreach ($principal_list as $value) {
            $stock = DB::table("iv_stock_ledger as a")
                                ->where("a.principal_id", $value->principal_id)
                                ->where("a.branch_id", $value->branch_id)
                                ->where("a.qtys", ">", 0)
                                ->get()
                                ->count();

            if ($stock > 0) {
                $this->sendEmail($value->principal_id, $value->branch_id);
            }
        }

        return 0;
    }

    private function sendEmail($principal_id, $branch_id) {
        $principal = MasterPrincipal::find($principal_id);

        $sendData = TransactionEmailPrincipal::where("branch_id", $branch_id)
                    ->where("principal_id", $principal_id)
                    ->where("description", "Stock Ledger")
                    ->first();

        if (isset($sendData)) {
            $list_to = explode(";", $sendData->email_to);
            $list_cc = explode(";", $sendData->email_cc);
            $list_bcc = explode(";", $sendData->email_bcc);

            $email_to = [];
            for ($i=0; $i < count($list_to); $i++) {
                if ( !empty($list_to[$i]) && $list_to[$i] !== "") {
                    $email_to[] = $list_to[$i];
                }
            }

            $email_cc = [];
            for ($i=0; $i < count($list_cc); $i++) {
                if ( !empty($list_cc[$i]) && $list_cc[$i] !== "") {
                    $email_cc[] = $list_cc[$i];
                }
            }

            $email_bcc = [];
            for ($i=0; $i < count($list_bcc); $i++) {
                if ( !empty($list_bcc[$i]) && $list_bcc[$i] !== "") {
                    $email_bcc[] = $list_bcc[$i];
                }
            }

            Mail::to($email_to)
                    ->cc($email_cc)
                    ->bcc($email_bcc)
                    ->send(new stockEmail($principal_id, $branch_id));

            $filename = $principal->short_name . "_stockledger.xlsx";
            Storage::delete($filename);
        }
    }
}
