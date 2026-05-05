<?php

namespace App\Http\Controllers\Report;

use App\Exports\Shad\OutboundExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Master\Principal as MasterPrincipal;

class DespatchController extends Controller
{
    public function index() {
        $company_id = Auth::user()->company_id;
        $principal_id = Auth::user()->principal->first()->id;

        $customer_list = DB::table("iv_customer as a")
                            ->where("a.company_id", $company_id)
                            ->where("a.principal_id", $principal_id)
                            ->where("a.active", "Yes")
                            ->orderBy("a.customer_name", "asc")
                            ->get();

        $store_list = DB::table("tm_store as a")
                            ->where("a.company_id", $company_id)
                            ->where("a.principal_id", $principal_id)
                            ->where("a.active", "Yes")
                            ->orderBy("a.store_name", "asc")
                            ->get();

        $data = [
            "customer_list" => $customer_list,
            "store_list" => $store_list
        ];

        return view('report.despatch-report.index', $data);
    }

    public function print(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $principal_id = $request->principal_id;

        if (!empty($request->customer_code_from) && !empty($request->customer_code_to)) {
            $customer_from = $request->customer_code_from;
            $customer_to = $request->customer_code_to;
        } else {
            if (!empty($request->customer_code_from) && empty($request->customer_code_to)) {
                $customer_from = $request->customer_code_from;
                $customer_to = "zzzzzzzzzzzzzzzzzzzz";
            } else if (empty($request->brand_code_from) && !empty($request->customer_code_to)) {
                $customer_from = "";
                $customer_to = $request->customer_code_to;
            } else {
                $customer_from = "";
                $customer_to = "zzzzzzzzzzzzzzzzzzzz";
            }
        }

        if (!empty($request->store_code_from) && !empty($request->store_code_to)) {
            $store_from = $request->store_code_from;
            $store_to = $request->store_code_to;
        } else {
            if (!empty($request->store_code_from) && empty($request->store_code_to)) {
                $store_from = $request->store_code_from;
                $store_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            } else if (empty($request->brand_code_from) && !empty($request->store_code_to)) {
                $store_from = "";
                $store_to = $request->store_code_to;
            } else {
                $store_from = "";
                $store_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            }
        }

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

        $order_list = DB::table("iv_outbound_job as a")
                    ->select(
                        "a.job_no",
                        "a.job_date",
                        "f.customer_code",
                        "f.customer_name",
                        "b.order_no",
                        "e.store_code",
                        "e.store_name",
                        "e.address1",
                        "e.address2",
                        "e.address3",
                        "e.address4",
                        "c.reference_no",
                        "g.product_code",
                        "h.product_name",
                        DB::raw("sum(g.qty) as qty"),
                        "g.puom"
                    )
                    ->join("iv_outbound_order as b", "a.id", "b.outbound_id")
                    ->join("iv_outbound_despatch as c", function($query) {
                        $query->on("b.outbound_id", "c.outbound_id")
                            ->on("b.customer_id", "c.customer_id");
                    })
                    ->leftJoin("tm_store as e", "c.store_id", "e.id")
                    ->join("iv_customer as f", "b.customer_id", "f.id")
                    ->join("iv_outbound_batch as g", function($query) {
                        $query->on("b.outbound_id", "g.outbound_id")
                            ->on("b.customer_id", "g.customer_id")
                            ->on("b.order_no", "g.order_no");
                    })
                    ->join("iv_product as h", "g.product_id", "h.id")
                    ->join("sm_user_branch as i", "a.branch_id", "i.branch_id")
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $principal_id)
                    ->where('i.user_id', $user_id)
                    ->where("a.confirmed_flag", "Yes")
                    ->whereBetween('a.job_date', [date($date_from), date($date_to)])
                    ->whereBetween(DB::raw("COALESCE(f.customer_code, '')"), [$customer_from, $customer_to])
                    ->whereBetween(DB::raw("COALESCE(e.store_code, '')"), [$store_from, $store_to])
                    ->groupBy(
                        "a.id",
                        "a.job_no",
                        "a.job_date",
                        "b.customer_id",
                        "f.customer_code",
                        "f.customer_name",
                        "b.order_no",
                        "e.store_code",
                        "e.store_name",
                        "e.address1",
                        "e.address2",
                        "e.address3",
                        "e.address4",
                        "c.reference_no",
                        "g.product_code",
                        "h.product_name",
                        "g.puom"
                    )
                    ->orderBy("f.customer_name", "ASC")
                    ->orderBy("b.order_no", "ASC")
                    ->get();

        $data = [
            "order_list" => $order_list
        ];

        return view("report.despatch-report.print", $data);
    }

    public function getList(Request $request) {
        $company_id = Auth::user()->company_id;

        $customer_list = DB::table("iv_customer as a")
                            ->where("a.company_id", $company_id)
                            ->where("a.principal_id", $request->principal_id)
                            ->where("a.active", "Yes")
                            ->orderBy("a.customer_name", "asc")
                            ->get();

        $store_list = DB::table("tm_store as a")
                            ->where("a.company_id", $company_id)
                            ->where("a.principal_id", $request->principal_id)
                            ->where("a.active", "Yes")
                            ->orderBy("a.store_name", "asc")
                            ->get();

        $data = [
            "customer_list" => $customer_list,
            "store_list" => $store_list
        ];

        return response()->json($data);
    }

    public function export(Request $request) {
        $company_id = Auth::user()->company_id;
        $principal_id = $request->principal_id;

        if (!empty($request->customer_code_from) && !empty($request->customer_code_to)) {
            $customer_from = $request->customer_code_from;
            $customer_to = $request->customer_code_to;
        } else {
            if (!empty($request->customer_code_from) && empty($request->customer_code_to)) {
                $customer_from = $request->customer_code_from;
                $customer_to = "zzzzzzzzzzzzzzzzzzzz";
            } else if (empty($request->brand_code_from) && !empty($request->customer_code_to)) {
                $customer_from = "";
                $customer_to = $request->customer_code_to;
            } else {
                $customer_from = "";
                $customer_to = "zzzzzzzzzzzzzzzzzzzz";
            }
        }

        if (!empty($request->store_code_from) && !empty($request->store_code_to)) {
            $store_from = $request->store_code_from;
            $store_to = $request->store_code_to;
        } else {
            if (!empty($request->store_code_from) && empty($request->store_code_to)) {
                $store_from = $request->store_code_from;
                $store_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            } else if (empty($request->brand_code_from) && !empty($request->store_code_to)) {
                $store_from = "";
                $store_to = $request->store_code_to;
            } else {
                $store_from = "";
                $store_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
            }
        }

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

		return Excel::download(new OutboundExport($company_id, $principal_id, $date_from, $date_to, $customer_from, $customer_to, $store_from, $store_to), $filename);
    }
}
