<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\WHchartMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Master\Principal as MasterPrincipal;
use App\Models\Transaction\Email as TransactionEmail;

class ChartController extends Controller
{
    public $user_id = null;
    public $company_id = null;
    public $principal_id = null;
    public $year_number = null;
    public $month_number = null;

    public function index(Request $request)
    {
        $this->company_id = Auth::user()->company_id;
        $this->principal_id = $request->principal_id;
        $this->year_number = \Carbon\Carbon::now()->year;
        $this->month_number = \Carbon\Carbon::now()->month;

        $periode = \Carbon\Carbon::create($this->year_number, $this->month_number, 1)->format("F") . " " . $this->year_number;

        $response = $this->transaction("quantity");

        $last_year = \Carbon\Carbon::now()->addYear(10)->year;

        $year_number = \Carbon\Carbon::now()->year;
        $month_number = \Carbon\Carbon::now()->month;

        $year_list = [];
        for ($i = 2020; $i < $last_year; $i++) {
            $year_list[] = $i;
        }

        $data = [
            "month_number" => $this->month_number,
            "year_list" => $year_list,
            "year_number" => $this->year_number,
            "periode" => json_encode($periode),
            "chartData" => json_encode($response)
        ];

        return view("dashboard.chart.index", $data);
    }

    public function getData(Request $request)
    {
        $this->company_id = Auth::user()->company_id;
        $this->year_number = $request->year_number;
        $this->month_number = $request->month_number;

        $periode = \Carbon\Carbon::create($this->year_number, $this->month_number, 1)->format("F") . " " . $this->year_number;

        $principal_id = $request->principal_id;

        $chart_title = [];
        $principal = [];
        $data = [];
        if ($principal_id == "All") {
            $principal_list = Auth::user()->principal;

            foreach ($principal_list as $key => $value) {
                $this->principal_id = $value->id;

                $chart_title[] = $value->principal_name . " - " . $periode;
                $principal[] = $value->id;

                $data[] = $this->transaction($request->report_type);
            }
        } else {
            $prin = MasterPrincipal::find($principal_id);

            $chart_title[] = $prin->principal_name . " - " . $periode;
            $principal[] = $prin->id;

            $this->principal_id = $principal_id;
            $data[] = $this->transaction($request->report_type);
        }

        $return = [
            "periode" => json_encode($chart_title),
            "principal" => json_encode($principal),
            "chart_data" => json_encode($data)
        ];

        return response()->json($return);
    }

    public function print(Request $request)
    {
        $data = $request->chartData;
        $time = \Carbon\Carbon::now()->format("dmy.His");

        $storage_path = 'public/pdf';
        $filename = "chart_$time.pdf";
        $filePath = $storage_path . '/' . $filename;

        $customPaper = array(0, 0, 500.00, 420.00);
        $pdf = \PDF::loadView("dashboard.chart.print", compact('data'))->setPaper($customPaper, 'portrait');

        Storage::put($filePath, $pdf->output());

        $fileurl = Storage::path($filePath);

        $sendData = TransactionEmail::find(1);

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

        // \Mail::to($email_to)->cc($email_cc)->bcc($email_bcc)->send(new WHchartMail($fileurl));

        Storage::delete($filePath);

        $message = ["success" => "Sukses"];

        return  response()->json($message);
    }

    public function transaction($type)
    {
        $date_start = \Carbon\Carbon::create($this->year_number, $this->month_number, 1);

        $date_finish = \Carbon\Carbon::create($this->year_number, $this->month_number, 1)->endOfMonth();

        $datediff = $date_start->diffInDays($date_finish) + 1;

        $label = [];
        $inbound = [];
        $outbound = [];
        $data = [];

        $data[] = ['Day', 'Inbound', 'Outbound'];
        for ($i = 1; $i <= $datediff; $i++) {
            $date = \Carbon\Carbon::create($this->year_number, $this->month_number, $i);

            if ($type == "WTV") {
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
            } else if ($type == "WTW") {
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
            } else if ($type == "WTQ") {
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

            $inbound = $jumlah_in == null ? 0 : $jumlah_in;
            $outbound = $jumlah_out == null ? 0 : $jumlah_out;

            $data[$i] = [$i, (int)$inbound, (int)$outbound];

            // $inbound[] = $jumlah_in == null ? 0 : $jumlah_in;
            // $outbound[] = $jumlah_out == null ? 0 : $jumlah_out;

            $label[] = $i;
        }

        // $data = [
        //     "label" => $label,
        //     "inbound" => $inbound,
        //     "outbound" => $outbound
        // ];

        return $data;
    }
}
