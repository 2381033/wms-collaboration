<?php

namespace App\Console\Commands;

use App\Exports\InboundExport;
use App\Mail\LedgerVTransactionEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Transaction\EmailPrincipal as TransactionEmailPrincipal;

class LedgerVTransactionReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LVTest:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notification to user about Differennt Stock in Ledger and Onhand.';

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
        $data = array();
        $principal_list = DB::table("iv_principal as a")
            ->join("iv_principal_branch as b", "a.id", "b.principal_id")
            ->where("active", "Yes")
            ->orderBy("a.id", "asc")
            ->orderBy("b.branch_id", "asc")
            ->get();

        foreach ($principal_list as $value) {
            $stock = DB::select("SELECT
                                    branch_id, principal_id, product_id, product_code, sum(qtytr) AS qty
                            FROM
                            (
                                    SELECT
                                        id, branch_id, principal_id, product_id, product_code, location_code,
                                        CASE
                                            WHEN job_type IN ('IMP', 'TFRI', 'ADJ+') THEN qty
                                            WHEN job_type IN ('EXP', 'TFRO', 'ADJ-') THEN (qty * -1)
                                            ELSE 0
                                        END AS qtytr
                                    FROM
                                        iv_stock_transaction ist
                                    WHERE
                                        branch_id = $value->branch_id AND
                                        principal_id = $value->principal_id
                                ) x
                            GROUP BY
                                branch_id,
                                principal_id,
                                product_id,
                                product_code
                            HAVING
                                qty > 0");

            foreach ($stock as $key => $value) {
                $query = DB::table("iv_stock_ledger")
                    ->select("id", "principal_id", "product_id", "product_code")
                    ->selectRaw("sum(qtyr) qtyr, sum(qtys) qtys, sum(qtya) qtya, sum(qtyp) qtyp")
                    ->where("branch_id", $value->branch_id)
                    ->where("principal_id", $value->principal_id)
                    ->where("product_id", $value->product_id)
                    ->where("product_code", $value->product_code)
                    ->groupBy("product_id", "product_code");
                // ->first();
                $ledger = $query->first();
                $Cledger = $query->count();

                if ($Cledger > 0) {
                    if ($value->qty != $ledger->qtys || $value->qty != ($ledger->qtya + $ledger->qtyp)) {
                        // echo $value->product_code." = ".$value->qty." | ".$ledger->qtys;
                        array_push($data, array(
                            'branch_id' => "$value->branch_id",
                            'principal_id' => "$ledger->principal_id",
                            'product_id' => "$ledger->product_id",
                            'product_code' => "$ledger->product_code",
                            'qtys' => "$ledger->qtys",
                            'qtya' => "$ledger->qtya",
                            'qtyp' => "$ledger->qtyp",
                            'qtyt' => "$value->qty"
                        ));
                    }
                }
            }
        }

        if (sizeof($data) > 0) {
            $data = (object) $data;
            $this->sendEmail($data);
        }

        return 0;
    }

    private function sendEmail($data)
    {
        $list_to = 'wahyudi.pratama@praweda.id';
        // $list_to = explode(";", $sendData->email_to);
        // $list_cc = explode(";", $sendData->email_cc);
        // $list_bcc = explode(";", $sendData->email_bcc);

        $email_to = 'wahyudi.pratama@praweda.id';

        Mail::to($email_to)
            ->send(new LedgerVTransactionEmail($data));

        // foreach ($list as $value) {
        //     $filename = "inbound_$value->job_no.xlsx";
        //     Storage::delete($filename);
        // }
    }
}
