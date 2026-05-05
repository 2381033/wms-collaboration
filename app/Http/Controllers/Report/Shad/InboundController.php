<?php

namespace App\Http\Controllers\Report\Shad;

use App\Exports\Shad\InboundExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Master\Principal as MasterPrincipal;

class InboundController extends Controller
{
    public function index() {
        return view('report.shad.inbound.index');
    }

    public function print(Request $request) {
        $company_id = Auth::user()->company_id;
        $principal_id = $request->principal_id;

        $date_from = '1990-01-01';
        $date_to = '2999-12-31';
        if (!empty($request->date_from) && !empty($request->date_to)) {
            $date_from = $request->date_from;
            $date_to = $request->date_to;
        } else if (!empty($request->date_from) && empty($request->date_to)) {
            $date_from = $request->date_from;
            $date_to = '2999-12-31';
        }

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $date_from);
        $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $date_to);
        $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $list = DB::table("iv_inbound_job as a")
                    ->select(
                        "a.job_no",
                        "a.job_date",
                        "a.description",
                        "b.vehicle_no",
                        "b.transporter_name",
                        "b.driver_name",
                        "e.manufactur_code",
                        "e.manufactur_name",
                        "c.product_code",
                        "d.product_name",
                        DB::raw("sum(c.qty) as qty"),
                        "c.puom"
                    )
                    ->join("iv_inbound_vehicle as b", "a.id", "b.inbound_id")
                    ->join("iv_inbound_batch as c", function($query) {
                        $query->on("b.inbound_id", "c.inbound_id")
                            ->on("b.vehicle_no", "c.vehicle_no");
                    })
                    ->join("iv_product as d", "c.product_id", "d.id")
                    ->leftjoin("iv_manufactur as e", "c.manufactur_id", "e.id")
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $principal_id)
                    ->where("a.confirmed_flag", "Yes")
                    ->whereBetween('a.job_date', [date($date_from), date($date_to)])
                    ->groupBy(
                        "a.job_no",
                        "a.job_date",
                        "a.description",
                        "b.vehicle_no",
                        "b.transporter_name",
                        "b.driver_name",
                        "e.manufactur_code",
                        "e.manufactur_name",
                        "c.product_code",
                        "d.product_name",
                        "c.puom"
                    )
                    ->get();

        $data = [
            "list" => $list
        ];

        return view("report.shad.inbound.print", $data);
    }

    public function export(Request $request) {
        $company_id = Auth::user()->company_id;
        $principal_id = $request->principal_id;

        $date_from = '1990-01-01';
        $date_to = '2999-12-31';
        if (!empty($request->date_from) && !empty($request->date_to)) {
            $date_from = $request->date_from;
            $date_to = $request->date_to;
        } else if (!empty($request->date_from) && empty($request->date_to)) {
            $date_from = $request->date_from;
            $date_to = '2999-12-31';
        }

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $date_from);
        $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $date_to);
        $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $principal = MasterPrincipal::find($principal_id);

        $time = \Carbon\Carbon::now()->format("dmy.His");
        $filename = "$principal->short_name-$time.xlsx";

		return Excel::download(new InboundExport($company_id, $principal_id, $date_from, $date_to), $filename);
    }
}
