<?php

namespace App\Http\Controllers\Transaction\CY;

use App\Exports\CYStockReport;
use App\Exports\CYTransactionReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function stockIndex() {
        $forwarder_list = DB::table("mt_forwarder as a")
                            ->select("a.*")
                            ->join("mt_forwarder_service as b", "a.id", "b.forwarder_id")
                            ->where("b.service_id", 3)   
                            ->orderBy("a.forwarder_name")                         
                            ->get();
        
        $data = [
            "forwarder_list"=>$forwarder_list
        ];

        return view('report.cy.stock-report', $data);
    }

    public function transactionIndex () {
        $forwarder_list = DB::table("mt_forwarder as a")
                            ->select("a.*")
                            ->join("mt_forwarder_service as b", "a.id", "b.forwarder_id")
                            ->where("b.service_id", 3)   
                            ->orderBy("a.forwarder_name")                         
                            ->get();
        
        $data = [
            "forwarder_list"=>$forwarder_list
        ];

        return view('report.cy.transaction-report', $data);
    }

    public function stock (Request $request) {
        $forwarder_id = $request->forwarder_id == "All" ? "%" : $request->forwarder_id;        

        $date_from = "1990-01-01";
        $date_to = "2999-12-31";             
        if (!empty($request->date_from) && !empty($request->date_to)) {            
            $date_from = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_to = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
        } else if (!empty($request->date_from) && empty($request->date_to)) {
            $date_from = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_to = "2999-12-31";     
        } 

        $date_from = \Carbon\Carbon::parse($date_from)->format("Y-m-d");
        $date_to = \Carbon\Carbon::parse($date_to)->format("Y-m-d");

        $list = DB::table("cy_stock_ledger as a")
                    ->select(
                        "a.*", 
                        "b.forwarder_name", 
                        "c.type_name as invoice_type", 
                        "d.size_name", 
                        "e.type_name",
                        DB::raw("CASE WHEN a.qtyp = 1 THEN 'Book' Else 'Stock' END as status")
                    )
                    ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                    ->join("cy_invoice_type as c", "a.invoice_type", "c.id")
                    ->join("iv_container_size as d", "a.size_id", "d.id")
                    ->join("iv_container_type as e", "a.type_id", "e.id")
                    ->where("a.qtys", 1)
                    ->where("a.forwarder_id", "like", $forwarder_id)
                    ->whereBetween(DB::raw("COALESCE(a.job_date, now())"), [$date_from, $date_to])
                    ->orderBy("b.forwarder_name", "ASC")
                    ->orderBy("a.job_date", "ASC")
                    ->get();
        
        $headOne = collect([
            [ "name"=>"Company Name", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Booking No", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Inbound No", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Inbound Date", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Reference No", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Vehicle No", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Driver Name", "rowspan"=>"1", "colspan"=>"1" ],
            [ "name"=>"Invoice Type", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Container Size", "rowspan"=>"1", "colspan"=>"1" ],
            [ "name"=>"Container Type", "rowspan"=>"1", "colspan"=>"1" ],
            [ "name"=>"Container Status", "rowspan"=>"1", "colspan"=>"1" ],
            [ "name"=>"Container No", "rowspan"=>"1", "colspan"=>"1" ],
            [ "name"=>"Status", "rowspan"=>"1", "colspan"=>"1" ],
        ]);

        $bodyOne = collect([
            [ "name"=>"Company Name", "field_name"=>"forwarder_name", "class"=>"left" ],
            [ "name"=>"Booking No", "field_name"=>"booking_no", "class"=>"left" ],
            [ "name"=>"Inbound No", "field_name"=>"job_no", "class"=>"left" ], 
            [ "name"=>"Inbound Date", "field_name"=>"job_date", "class"=>"left" ], 
            [ "name"=>"Reference No", "field_name"=>"reference_no", "class"=>"left" ], 
            [ "name"=>"Vehicle No", "field_name"=>"vehicle_no", "class"=>"left" ], 
            [ "name"=>"Driver Name", "field_name"=>"driver_name", "class"=>"left" ], 
            [ "name"=>"Invoice Type", "field_name"=>"invoice_type", "class"=>"left" ], 
            [ "name"=>"Container Size", "field_name"=>"size_name", "class"=>"left" ], 
            [ "name"=>"Container Type", "field_name"=>"type_name", "class"=>"left" ], 
            [ "name"=>"Container Status", "field_name"=>"container_status", "class"=>"left" ], 
            [ "name"=>"Container No", "field_name"=>"container_no", "class"=>"left" ], 
            [ "name"=>"Status", "field_name"=>"status", "class"=>"left" ], 
        ]);

        $listData = [];
        foreach ($list as $value) {
            $listData[] = [
                "forwarder_name"=>$value->forwarder_name,
                "booking_no"=>$value->booking_no,
                "job_no"=>$value->job_no,
                "job_date"=>\Carbon\Carbon::parse($value->job_date)->format("d-m-Y"),
                "reference_no"=>$value->reference_no,
                "vehicle_no"=>$value->vehicle_no,
                "driver_name"=>$value->driver_name,
                "invoice_type"=>$value->invoice_type,
                "size_name"=>$value->size_name,
                "type_name"=>$value->type_name,
                "container_status"=>$value->container_status,
                "container_no"=>$value->container_no,
                "status"=>$value->status,
            ];
        }

        $data = [
            "title"=>"CY Handling - Stock Report",
            "css"=>"landscape",
            "headOne"=>$headOne->toArray(),
            "bodyOne"=>$bodyOne->toArray(),
            "listData"=>$listData,
            "columnCount"=>13
        ];

        return view("report", $data);
    }

    public function transaction (Request $request) {
        $forwarder_id = $request->forwarder_id == "All" ? "%" : $request->forwarder_id;

        $job_type = $request->job_type == "All" ? "%" : $request->job_type;

        $date_from = "1990-01-01";
        $date_to = "2999-12-31";             
        if (!empty($request->date_from) && !empty($request->date_to)) {            
            $date_from = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_to = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
        } else if (!empty($request->date_from) && empty($request->date_to)) {
            $date_from = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_to = "2999-12-31";     
        } 

        $date_from = \Carbon\Carbon::parse($date_from)->format("Y-m-d");
        $date_to = \Carbon\Carbon::parse($date_to)->format("Y-m-d");

        $list = DB::table("cy_stock_transaction as a")
                    ->select(
                        "a.*", 
                        "b.forwarder_name", 
                        "c.type_name as invoice_type", 
                        "d.size_name", 
                        "e.type_name"
                    )
                    ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                    ->join("cy_invoice_type as c", "a.invoice_type", "c.id")
                    ->join("iv_container_size as d", "a.size_id", "d.id")
                    ->join("iv_container_type as e", "a.type_id", "e.id")
                    ->where("a.forwarder_id", "like", $forwarder_id)
                    ->where("a.job_type", "like", $job_type)
                    ->whereBetween(DB::raw("COALESCE(a.job_date, now())"), [$date_from, $date_to])
                    ->orderBy("b.forwarder_name", "ASC")
                    ->orderBy("a.booking_no", "ASC")
                    ->orderBy("a.job_type", "ASC")
                    ->orderBy("a.job_date", "ASC")
                    ->get();

        $headOne = collect([
            [ "name"=>"Company Name", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Booking No", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Job No", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Job Date", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Reference No", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Vehicle No", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Driver Name", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Invoice Type", "rowspan"=>"1", "colspan"=>"1" ], 
            [ "name"=>"Container Size", "rowspan"=>"1", "colspan"=>"1" ],
            [ "name"=>"Container Type", "rowspan"=>"1", "colspan"=>"1" ],
            [ "name"=>"Container Status", "rowspan"=>"1", "colspan"=>"1" ],
            [ "name"=>"Container No", "rowspan"=>"1", "colspan"=>"1" ],
            [ "name"=>"Job Type", "rowspan"=>"1", "colspan"=>"1" ],
        ]);

        $bodyOne = collect([
            [ "name"=>"Company Name", "field_name"=>"forwarder_name", "class"=>"left" ],
            [ "name"=>"Booking No", "field_name"=>"booking_no", "class"=>"left" ],
            [ "name"=>"Document No", "field_name"=>"job_no", "class"=>"left" ], 
            [ "name"=>"Document Date", "field_name"=>"job_date", "class"=>"left" ], 
            [ "name"=>"Reference No", "field_name"=>"reference_no", "class"=>"left" ], 
            [ "name"=>"Vehicle No", "field_name"=>"vehicle_no", "class"=>"left" ], 
            [ "name"=>"Driver Name", "field_name"=>"driver_name", "class"=>"left" ], 
            [ "name"=>"Invoice Type", "field_name"=>"invoice_type", "class"=>"left" ], 
            [ "name"=>"Container Size", "field_name"=>"size_name", "class"=>"left" ], 
            [ "name"=>"Container Type", "field_name"=>"type_name", "class"=>"left" ], 
            [ "name"=>"Container Status", "field_name"=>"container_status", "class"=>"left" ], 
            [ "name"=>"Container No", "field_name"=>"container_no", "class"=>"left" ], 
            [ "name"=>"Job Type", "field_name"=>"job_type", "class"=>"left" ], 
        ]);

        $listData = [];
        foreach ($list as $value) {
            $listData[] = [
                "forwarder_name"=>$value->forwarder_name,
                "booking_no"=>$value->booking_no,
                "job_no"=>$value->reference_job,
                "job_date"=>\Carbon\Carbon::parse($value->job_date)->format("d-m-Y"),
                "reference_no"=>$value->reference_no,
                "vehicle_no"=>$value->vehicle_no,
                "driver_name"=>$value->driver_name,
                "invoice_type"=>$value->invoice_type,
                "size_name"=>$value->size_name,
                "type_name"=>$value->type_name,
                "container_status"=>$value->container_status,
                "container_no"=>$value->container_no,
                "job_type"=>$value->job_type,
            ];
        }

        $data = [
            "title"=>"CY Handling - Transaction Report",
            "css"=>"landscape",
            "headOne"=>$headOne->toArray(),
            "bodyOne"=>$bodyOne->toArray(),
            "listData"=>$listData,
            "columnCount"=>12
        ];

        return view("report", $data);
    }
 
	public function stockExport(Request $request) {
        $forwarder_id = $request->forwarder_id == "All" ? "%" : $request->forwarder_id;

        $time = \Carbon\Carbon::now()->format("dmy.His");

        $date_from = "1990-01-01";
        $date_to = "2999-12-31";             
        if (!empty($from) && !empty($to)) {
            $date_from = $from;
            $date_to = $to;
        } else if (!empty($from) && empty($to)) {
            $date_from = $from;
            $date_to = "2999-12-31";     
        } 

        $filename = "cy_stock_$time.xlsx";

		return Excel::download(new CYStockReport($forwarder_id, $date_from, $date_to), $filename);
    }
 
	public function transactionExport(Request $request) {
        $forwarder_id = $request->forwarder_id == "All" ? "%" : $request->forwarder_id;

        $job_type = $request->job_type == "All" ? "%" : $request->job_type;

        $time = \Carbon\Carbon::now()->format("dmy.His");

        $date_from = "1990-01-01";
        $date_to = "2999-12-31";             
        if (!empty($from) && !empty($to)) {
            $date_from = $from;
            $date_to = $to;
        } else if (!empty($from) && empty($to)) {
            $date_from = $from;
            $date_to = "2999-12-31";     
        } 

        $filename = "cy_transaction_$time.xlsx";

		return Excel::download(new CYTransactionReport($forwarder_id, $date_from, $date_to, $job_type), $filename);
    }

    public function suratJalan($id) {
        $view = DB::table("cy_outbound as a")
                    ->select(
                        "a.*",
                        "b.size_name",
                        "c.type_name",
                        "d.forwarder_name",
                        "e.container_status",
                        "f.inspected_date"
                    )
                    ->join("iv_container_size as b", "a.size_id", "b.id")
                    ->join("iv_container_type as c", "a.type_id", "c.id")
                    ->join("mt_forwarder as d", "a.forwarder_id", "d.id")
                    ->join("cy_stock_ledger as e", "a.serial_id", "e.id")
                    ->join("cy_checklist_header as f", "a.checklist_no", "f.job_no")
                    ->where("a.id", $id)
                    ->first();

        $data = [
            "view"=>$view
        ];

        return view("transaction.cy.outbound.surat-jalan", $data);
    }
}