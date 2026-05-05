<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Principal as MasterPrincipal;

class DashboardChart extends Command
{
    public $year = null;
    public $month = null;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:warehouse-chart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->year = \Carbon\Carbon::now()->year;
        $this->month = 5; //\Carbon\Carbon::now()->month;

        $principal_list = MasterPrincipal::where("active", "Yes")->get();

        // foreach ($principal_list as $key => $value) {
        // $data = $this->transaction("quantity", 1, 2);

        // $this->sendView($data);
        // }
        $request = Request::create("/dashboard/chart/2", 'GET');
        $this->info(app()->make(\Illuminate\Contracts\Http\Kernel::class)->handle($request));

        return 0;
    }

    private function sendView($jsonData)
    {
        $periode = \Carbon\Carbon::create($this->year, $this->month, 1)->format("F") . " " . $this->year;

        $data = [
            "periode" => json_encode($periode),
            "chartData" => json_encode($jsonData)
        ];

        view("dashboard.chart.warehouse", $data);
    }

    public function transaction($type, $company_id, $principal_id)
    {
        $date_start = \Carbon\Carbon::create($this->year, $this->month, 1);
        $date_finish = \Carbon\Carbon::create($this->year, $this->month, 1)->endOfMonth();

        $datediff = $date_start->diffInDays($date_finish) + 1;

        $label = [];
        $inbound = [];
        $outbound = [];
        $data = [];

        $data[] = ['Day', 'Inbound', 'Outbound'];
        for ($i = 1; $i <= $datediff; $i++) {
            $date = \Carbon\Carbon::create($this->year, $this->month, $i);

            if ($type == "volume") {
                $value = DB::table("iv_stock_transaction as a")
                    ->select(
                        DB::raw("sum(case when a.job_type = 'IMP' then a.qty * b.volume end) as inbound"),
                        DB::raw("sum(case when a.job_type = 'EXP' then a.qty * b.volume end) as outbound")
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->where("a.company_id", $company_id)
                    ->where("a.principal_id", $principal_id)
                    ->whereMonth("a.job_date", $this->month)
                    ->whereYear("a.job_date", $this->year)
                    ->whereIn("a.job_type", ["IMP", "EXP"])
                    ->where("a.job_date", $date)
                    ->first();
            } else if ($type == "weight") {
                $value = DB::table("iv_stock_transaction as a")
                    ->select(
                        DB::raw("sum(case when a.job_type = 'IMP' then a.qty * b.gross_weight end) as inbound"),
                        DB::raw("sum(case when a.job_type = 'EXP' then a.qty * b.gross_weight end) as outbound")
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->where("a.company_id", $company_id)
                    ->where("a.principal_id", $principal_id)
                    ->whereMonth("a.job_date", $this->month)
                    ->whereYear("a.job_date", $this->year)
                    ->whereIn("a.job_type", ["IMP", "EXP"])
                    ->where("a.job_date", $date)
                    ->first();
            } else if ($type == "quantity") {
                $value = DB::table("iv_stock_transaction as a")
                    ->select(
                        DB::raw("sum(case when a.job_type = 'IMP' then a.qty end) as inbound"),
                        DB::raw("sum(case when a.job_type = 'EXP' then a.qty end) as outbound")
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->where("a.company_id", $company_id)
                    ->where("a.principal_id", $principal_id)
                    ->whereMonth("a.job_date", $this->month)
                    ->whereYear("a.job_date", $this->year)
                    ->whereIn("a.job_type", ["IMP", "EXP"])
                    ->where("a.job_date", $date)
                    ->first();
            }

            $jumlah_in = 0;
            if (isset($value)) {
                $jumlah_in = $value->inbound;
            }

            $jumlah_out = 0;
            if (isset($value)) {
                $jumlah_out = $value->outbound;
            }

            $inbound = $jumlah_in == null ? 0 : $jumlah_in;
            $outbound = $jumlah_out == null ? 0 : $jumlah_out;

            $data[$i] = [$i, (int)$inbound, (int)$outbound];
        }

        return $data;
    }
}
