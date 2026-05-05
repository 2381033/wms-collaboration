<?php

namespace App\Http\Controllers\Transaction\Export\Outbound;

use App\Exports\CLPExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function clp($id)
    {
        $stock = DB::table("ex_stock_ledger as a")
            ->select(
                DB::raw("sum(a.quantity) AS qty_cargo"),
                DB::raw("sum(a.cbm) AS cbm"),
                DB::raw("sum(a.weight) AS weight"),
                DB::raw("count(a.total_pallet) AS total_pallet"),
            )
            ->join("ex_outbound_detail as b", "a.serial_no", "b.serial_no")
            ->where("b.job_id", $id)
            ->first();

        $header = \App\Models\Transaction\Export\OutboundHeader::find($id);
        $header->qty_cargo = $stock->qty_cargo;
        $header->cbm = $stock->cbm;
        $header->weight = $stock->weight;
        $header->total_pallet = $stock->total_pallet;
        $header->save();

        $order = DB::table("ex_outbound_detail as a")
            ->select(
                "a.*",
                "b.cbm",
                "b.weight",
                "b.total_pallet",
                "c.consignee_name",
                "e.shipper_name",
                "d.location_code"
            )
            ->join("ex_outbound_order as b", "a.order_id", "=", "b.id")
            ->join("mt_consignee as c", "b.consignee_id", "=", "c.id")
            ->join("ex_stock_ledger as d", function ($join) {
                $join->on("a.serial_no", "=", "d.serial_no")
                    ->whereRaw("d.id = (SELECT MAX(id) FROM ex_stock_ledger WHERE serial_no = a.serial_no)");
            })
            ->join("mt_shipper as e", "d.shipper_id", "=", "e.id")
            ->where("a.job_id", $id)
            ->orderBy("b.id", "ASC")
            ->orderBy("a.id", "ASC")
            ->get();
        $totalQty = array_sum($order->pluck('quantity')->toArray());
        $serial_no = $order->pluck('serial_no')->toArray();
        $stockLedger = DB::table('ex_stock_ledger')
            ->select('peb_no', 'total_pallet')
            ->whereIn('serial_no', $serial_no)->get();
        $totalPallet = $stockLedger
            ->unique('peb_no')
            ->mapWithKeys(function ($item) {
                return [$item->peb_no => $item->total_pallet];
            });
        $totalPalletSum = $totalPallet->sum();

        $job = DB::table("ex_outbound_header as a")
            ->select(
                "a.*",
                "b.forwarder_name"
            )
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->where("a.id", $id)
            ->first();
        $size = DB::table('iv_container_size')->select('size_name')->where('id', $job->size_id)->value('size_name') ?? ' ';
        $data = [
            "job" => $job,
            "order_list" => $order,
            "size" => $size,
            "totalQty" => $totalQty,
            "totalPallet" => $totalPalletSum,
        ];

        return view("transaction.export.report.clp", $data);
    }

    public function index()
    {
        $forwarder_list = DB::table("mt_forwarder as a")
            ->select("a.*")
            ->join("mt_forwarder_service as b", "a.id", "b.forwarder_id")
            ->where("b.service_id", 2)
            ->orderBy("a.forwarder_name")
            ->get();

        $shipper_list = DB::table("mt_shipper as a")
            ->select("a.*")
            ->orderBy("a.shipper_name")
            ->get();

        $container_list = DB::table("ex_outbound_header as a")
            ->select("a.container_no")
            ->where("a.forwarder_id", "like", "%")
            ->orderBy("a.container_no")
            ->get();

        $data = [
            "forwarder_list" => $forwarder_list,
            "shipper_list" => $shipper_list,
            "container_list" => $container_list
        ];

        return view('transaction.export.report.index', $data);
    }

    public function clpDetail(Request $request)
    {
        $forwarder_id = $request->forwarder_id == "All" ? "%" : $request->forwarder_id;
        $shipper_id = $request->shipper_id == "All" ? "%" : $request->shipper_id;
        $container_no = $request->container_no == "" ? "%" : $request->container_no;

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

        $container = DB::table("ex_outbound_header as a")
            ->select(
                "a.container_no"
            )
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->join("ex_outbound_order as c", "a.id", "c.job_id")
            ->join("ex_outbound_detail as d", function ($query) {
                $query->on("c.job_id", "d.job_id")
                    ->on("c.id", "d.order_id");
            })
            ->join("mt_consignee as e", "c.consignee_id", "e.id")
            ->join("ex_stock_ledger as f", "d.serial_no", "f.serial_no")
            ->join("mt_shipper as g", "f.shipper_id", "g.id")
            ->where("a.forwarder_id", "like", $forwarder_id)
            ->where("f.shipper_id", "like", $shipper_id)
            ->whereBetween('a.job_date', [date($date_from), date($date_to)])
            ->groupBy("a.container_no")
            ->orderBy("a.container_no", "ASC")
            ->get();

        $detail = DB::table("ex_outbound_header as a")
            ->select(
                "a.*",
                "b.forwarder_name",
                "c.po_number",
                "c.peb_no",
                "d.serial_no",
                "e.consignee_name",
                "g.shipper_name",
                "d.quantity",
                "d.status_flag as action"
            )
            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
            ->join("ex_outbound_order as c", "a.id", "c.job_id")
            ->join("ex_outbound_detail as d", function ($query) {
                $query->on("c.job_id", "d.job_id")
                    ->on("c.id", "d.order_id");
            })
            ->join("mt_consignee as e", "c.consignee_id", "e.id")
            ->join("ex_stock_ledger as f", "d.serial_no", "f.serial_no")
            ->join("mt_shipper as g", "f.shipper_id", "g.id")
            ->where(DB::raw("COALESCE(a.forwarder_id, 0)"), "LIKE", $forwarder_id)
            ->where(DB::raw("COALESCE(f.shipper_id, 0)"), "LIKE", $shipper_id)
            ->whereBetween('a.job_date', [date($date_from), date($date_to)])
            ->where("a.container_no", "LIKE", $container_no)
            ->orderBy("a.container_no", "ASC")
            ->orderBy("c.id", "ASC")
            ->orderBy("d.id", "ASC")
            ->get();

        $data = [
            "container_list" => $container,
            "detail_list" => $detail
        ];

        return view("transaction.export.report.clp-detail", $data);
    }

    public function export(Request $request)
    {
        $forwarder_id = $request->forwarder_id == "All" ? "%" : $request->forwarder_id;
        $shipper_id = $request->shipper_id == "All" ? "%" : $request->shipper_id;
        $container_no = $request->container_no == "" ? "%" : $request->container_no;

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

        $filename = "clp_$time.xlsx";

        return Excel::download(new CLPExport($forwarder_id, $shipper_id, $date_from, $date_to, $container_no), $filename);
    }
}
