<?php

namespace App\Http\Controllers\Report;

use App\Exports\PendingOutboundExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Master\Principal as MasterPrincipal;

class PendingTransactionController extends Controller
{
    public function index()
    {
        return view("report.pending-transaction.index");
    }

    public function print(Request $request)
    {
        $company_id = Auth::user()->company_id;

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
        $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
        $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $principal = MasterPrincipal::find($request->principal_id);

        $list = DB::table("iv_outbound_job as a")
            ->select(
                "a.job_no",
                "a.job_date",
                "a.description",
                "c.customer_name",
                "b.order_no",
                "e.product_code",
                "e.product_name",
                DB::raw("sum(d.qty) as qty")
            )
            ->join("iv_outbound_order as b", "a.id", "b.outbound_id")
            ->join("iv_customer as c", "b.customer_id", "c.id")
            ->join("iv_outbound_detail as d", "b.id", "d.order_id")
            ->join("iv_product as e", "d.product_id", "e.id")
            ->where("a.company_id", $company_id)
            ->where("a.principal_id", $request->principal_id)
            ->whereBetween('a.job_date', [date($date_from), date($date_to)])
            ->whereNotIn("a.confirmed_flag", ["Yes", "Cancel"])
            ->orderBy("a.job_no", "ASC")
            ->orderBy("c.customer_name", "ASC")
            ->groupBy(
                "a.job_no",
                "a.job_date",
                "a.description",
                "c.customer_name",
                "b.order_no",
                "e.product_code",
                "e.product_name"
            )
            ->get();

        $data = [
            'title' => 'Pending Outbound Report',
            "principal_name" => $principal->principal_name,
            "date_from" => $date_from,
            "date_to" => $date_to,
            "list" => $list
        ];

        return view("report.pending-transaction.print", $data);
    }

    public function export(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $principal_id = $request->principal_id;
        $principal = MasterPrincipal::find($request->principal_id);

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

        $time = \Carbon\Carbon::now()->format("dmy.His");

        $filename = "$principal->short_name-pending-$time.xlsx";

        return Excel::download(new PendingOutboundExport($company_id, $principal_id, $date_from, $date_to), $filename);
    }
}
