<?php

namespace App\Http\Controllers;

use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public $user_id = null;
    public $company_id = null;
    public $principal_id = null;
    public $year_number = null;
    public $month_number = null;

    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (Auth::check()) {
            // $last_year = \Carbon\Carbon::now()->addYear(1)->year;
            // $year_list = [];
            // for ($i = 2021; $i < $last_year; $i++) {
            //     $year_list[] = $i;
            // }

            // $principal_count = Auth::user()->principal->count();

            // if ($principal_count > 0) {
            //     $this->principal_id = Auth::user()->principal->count();
            // }

            // $this->year_number = \Carbon\Carbon::now()->year;
            // $this->month_number = \Carbon\Carbon::now()->month;

            // $data = [
            //     "year_list" => $year_list,
            //     "year_number" => $this->year_number,
            //     "month_number" => $this->month_number,
            // ];
            $branch    = $this->getBranch();
            $principal = $this->getPrincipal();

            return view('dashboard.index', compact('branch', 'principal'));
        } else {
            return view('home');
        }
    }

    private function myBranch()
    {
        $branch = DB::table('sm_user_branch')
            ->where('user_id', Auth::user()->id)
            ->get()->pluck('branch_id')->toArray();

        return $branch;
    }

    private function getBranch()
    {
        $branch = DB::table('mt_branch')
            ->whereIn('id', $this->myBranch())
            ->get();

        return $branch;
    }

    private function getPrincipal()
    {
        $principal_id = DB::table('users_principal')
            ->where('user_id', Auth::user()->id)
            ->pluck('principal_id')->toArray();
        $data = DB::table('iv_principal')
            ->orderBy('principal_name', 'ASC')
            ->whereIn('id', $principal_id)
            ->get();

        return $data;
    }

    public function generate(Request $request)
    {
        $this->company_id = Auth::user()->company_id;
        $this->user_id = Auth::user()->id;
        $this->principal_id = $request->principal_id;
        $this->month_number = $request->month_number;
        $this->year_number = $request->year_number;

        $date = new \Carbon\Carbon("$this->year_number-$this->month_number-01");

        $periode = \Carbon\Carbon::create($this->year_number, $this->month_number, 1)->format("F") . " " . $this->year_number;

        $bulan_list = [];

        for ($i = 1; $i <= 12; $i++) {
            $bulan_list[] = date('F', mktime(0, 0, 0, $i, 10));
        }

        $issue = $this->issueReason();

        $issue_label = $issue[0];
        $issue_data = $issue[1];

        $warehouse_daily = $this->warehouseDaily();
        $inbound_daily = $warehouse_daily[0];
        $outbound_daily = $warehouse_daily[1];

        $mtdData = $this->mtdChart();
        $mtd_occupancy = $mtdData[0];
        $mtd_inbound = $mtdData[1];
        $mtd_outbound = $mtdData[2];
        $mtd_label = $mtdData[3];

        $occupancy = $this->occupancyChart();
        $occupancy_data = $occupancy[0];
        $occupancy_label = $occupancy[1];

        $occupancyMTDChart = (new LarapexChart)
            ->barChart()
            ->setTitle("Occupancy (MTD) $periode.")
            ->addData('Occupancy', $mtd_occupancy)
            ->setXAxis($mtd_label)
            ->setGrid();

        $inboundMDTChart = (new LarapexChart)
            ->barChart()
            ->setTitle("Inbound (MTD) $periode.")
            ->addData('Confirm', $mtd_inbound)
            ->setXAxis($mtd_label)
            ->setColors(['#2ccdc9'])
            ->setGrid();

        $outboundMDTChart = (new LarapexChart)
            ->barChart()
            ->setTitle("Outbound (MTD) $periode.")
            ->addData('Confirm', $mtd_outbound)
            ->setXAxis($mtd_label)
            ->setColors(['#D32F2F'])
            ->setGrid();

        $occupancyChart = (new LarapexChart)
            ->pieChart()
            ->setTitle('Occupancy Status.')
            ->setSubtitle('Today Status By Percentage.')
            ->addData($occupancy_data)
            ->setColors(['#D32F2F', '#2ccdc9'])
            ->setLabels($occupancy_label)
            ->setDataLabels();

        $issueChart = (new LarapexChart)
            ->pieChart()
            ->setTitle('Issue Reason.')
            ->setSubtitle('Monthly Rating.')
            ->addData($issue_data)
            ->setLabels($issue_label)
            ->setDataLabels();

        $data = [
            "inbound_daily" => $inbound_daily,
            "outbound_daily" => $outbound_daily,
            "occupancyChart" => $occupancyChart,
            "occupancyMTDChart" => $occupancyMTDChart,
            "inboundMDTChart" => $inboundMDTChart,
            "outboundMDTChart" => $outboundMDTChart,
            "issueChart" => $issueChart,
            "periode" => $periode
        ];

        return view('dashboard.generate', $data);
    }

    public function transaction($type)
    {
        $date_start = \Carbon\Carbon::create($this->year_number, $this->month_number, 1);
        $date_finish = \Carbon\Carbon::create($this->year_number, $this->month_number, 1)->endOfMonth();

        $datediff = $date_start->diffInDays($date_finish) + 1;

        $label = [];
        $inbound = [];
        $outbound = [];
        for ($i = 1; $i <= $datediff; $i++) {
            $date = \Carbon\Carbon::create($this->year_number, $this->month_number, $i);

            if ($type == "volume") {
                $value = DB::table("iv_stock_transaction as a")
                    ->select(
                        DB::raw("sum(case when a.job_type = 'IMP' then a.qty * b.volume end) as inbound"),
                        DB::raw("sum(case when a.job_type = 'EXP' then a.qty * b.volume end) as outbound")
                    )
                    ->join("iv_product as b", "a.product_id", "b.id")
                    ->where("a.company_id", $this->company_id)
                    ->where("a.principal_id", $this->principal_id)
                    ->whereMonth("a.job_date", $this->month_number)
                    ->whereYear("a.job_date", $this->year_number)
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
                    ->where("a.company_id", $this->company_id)
                    ->where("a.principal_id", $this->principal_id)
                    ->whereMonth("a.job_date", $this->month_number)
                    ->whereYear("a.job_date", $this->year_number)
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
                    ->where("a.company_id", $this->company_id)
                    ->where("a.principal_id", $this->principal_id)
                    ->whereMonth("a.job_date", $this->month_number)
                    ->whereYear("a.job_date", $this->year_number)
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

            $inbound[] = $jumlah_in == null ? 0 : $jumlah_in;
            $outbound[] = $jumlah_out == null ? 0 : $jumlah_out;

            $label[] = $i;
        }

        $data = [
            "label" => $label,
            "inbound" => $inbound,
            "outbound" => $outbound
        ];

        return $data;
    }

    public function occupancyChart()
    {
        $location_count = DB::table("iv_stock_ledger as a")
            ->select(
                DB::raw("count(a.location_code) as total")
            )
            ->where("a.principal_id", $this->principal_id)
            // ->where("a.area_id", 1)
            ->where("a.qtys", ">", 0)
            ->groupBy("a.location_code")
            ->get()
            ->count();

        $capacity = 450;

        $empty = $capacity - $location_count;

        $occupancy_label = [];
        $occupancy_data = [];

        $percent_occupied = $location_count / $capacity * 100;
        $percent_empty = $empty / $capacity * 100;
        $occupancy_data[] = $percent_occupied;
        $occupancy_data[] = $percent_empty;
        $occupancy_label[] = "Occupied "; // . number_format($percent_occupied, 2);
        $occupancy_label[] = "Empty "; // . number_format($percent_empty, 2);

        $data = [
            $occupancy_data,
            $occupancy_label
        ];

        return $data;
    }

    public function mtdChart()
    {
        $date_start = \Carbon\Carbon::create($this->year_number, $this->month_number, 1);
        $date_finish = \Carbon\Carbon::create($this->year_number, $this->month_number, 1)->endOfMonth();

        $end_day = $date_start->diffInDays($date_finish) + 1;

        $label = [];
        $pallet = [];
        $out_picked = [];
        $in_receipt = [];
        for ($i = 1; $i <= $end_day; $i++) {
            $date = \Carbon\Carbon::create($this->year_number, $this->month_number, $i);

            $value = DB::table("iv_occupancy_daily as a")
                ->where("a.principal_id", $this->principal_id)
                ->where("a.transaction_date", $date)
                ->first();

            $pick_data = DB::table("iv_stock_transaction as a")
                ->where("a.principal_id", $this->principal_id)
                ->where("a.job_date", $date)
                ->where("a.job_type", "EXP")
                ->sum("a.qty");

            $receipt_data = DB::table("iv_stock_transaction as a")
                ->where("a.principal_id", $this->principal_id)
                ->where("a.job_date", $date)
                ->where("a.job_type", "IMP")
                ->sum("a.qty");

            $jumlah = 0;
            if (isset($value)) {
                $jumlah = $value->qty;
            }

            $pallet[] = $jumlah == null ? 0 : $jumlah;

            $picked = 0;
            if (isset($pick_data)) {
                $picked = $pick_data;
            }

            $receipt = 0;
            if (isset($receipt_data)) {
                $receipt = $receipt_data;
            }

            $out_picked[] = $picked == null ? 0 : (int)$picked;
            $in_receipt[] = $receipt == null ? 0 : (int)$receipt;

            $label[] = $i;
        }

        $data = [
            $pallet,
            $in_receipt,
            $out_picked,
            $label
        ];

        return $data;
    }

    public function warehouseDaily()
    {
        $today = \Carbon\Carbon::today();

        $inbound = DB::table("iv_inbound_detail as a")
            ->select(
                DB::raw("sum(a.qty) as total_orders"),
                DB::raw("sum(CASE WHEN a.received_flag = 'Yes' AND a.putaway_flag = 'No' AND a.confirmed_flag = 'No' THEN a.qty ELSE 0 END) as receipt"),
                DB::raw("sum(CASE WHEN a.received_flag = 'Yes' AND a.putaway_flag = 'Yes' AND a.confirmed_flag = 'No' THEN a.qty ELSE 0 END) as putaway"),
                DB::raw("sum(CASE WHEN a.received_flag = 'Yes' AND a.putaway_flag = 'Yes' AND a.confirmed_flag = 'Yes' THEN a.qty ELSE 0 END) as confirmed")
            )
            ->join("iv_inbound_job as b", "a.inbound_id", "b.id")
            ->where("a.principal_id", $this->principal_id)
            ->where("b.job_date", $today)
            ->where("a.confirmed_flag", "Yes")
            ->first();

        $outbound = DB::table("iv_outbound_detail as a")
            ->select(
                DB::raw("sum(a.qty) as total_orders"),
                DB::raw("sum(CASE WHEN a.picking_flag = 'No' AND a.confirmed_flag = 'No' THEN a.qty ELSE 0 END) as release_pick"),
                DB::raw("sum(CASE WHEN a.picking_flag = 'Yes' AND a.confirmed_flag = 'No' THEN a.qty ELSE 0 END) as in_pick"),
                DB::raw("sum(CASE WHEN a.picking_flag = 'Yes' AND a.confirmed_flag = 'Yes' THEN a.qty ELSE 0 END) as picked")
            )
            ->join("iv_outbound_job as b", "a.outbound_id", "b.id")
            ->where("a.principal_id", $this->principal_id)
            ->where("b.job_date", $today)
            ->where("a.confirmed_flag", "Yes")
            ->first();

        $data = [
            $inbound,
            $outbound,
        ];

        return $data;
    }

    public function issueReason()
    {
        $today = \Carbon\Carbon::today();

        for ($i = 1; $i <= 5; $i++) {
            $jumlah = DB::table("iv_issue_reason as a")
                ->where("a.principal_id", $this->principal_id)
                ->where("a.rating", $i)
                // ->where("a.job_date", $today)
                ->whereMonth("a.job_date", $this->month_number)
                ->whereYear("a.job_date", $this->year_number)
                ->get()
                ->count();

            $data[] = $jumlah;
            if ($i == 1) {
                // $label[] = "Bad : $jumlah" ;
                $label[] = "Bad";
            } else if ($i == 2) {
                // $label[] = "Poor : $jumlah";
                $label[] = "Poor";
            } else if ($i == 3) {
                // $label[] = "Fair : $jumlah";
                $label[] = "Fair";
            } else if ($i == 4) {
                // $label[] = "Good : $jumlah";
                $label[] = "Good";
            } else if ($i == 5) {
                // $label[] = "Excellent : $jumlah";
                $label[] = "Excellent";
            }
        }

        $result = [
            $label,
            $data
        ];

        return $result;
    }
}
