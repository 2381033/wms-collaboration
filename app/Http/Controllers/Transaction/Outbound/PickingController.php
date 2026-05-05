<?php

namespace App\Http\Controllers\Transaction\Outbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Outbound\Detail as outboundDetails;
use App\Models\Transaction\Stock\Ledger as stockLedger;
use App\Models\Transaction\Outbound\Batch as outboundBatch;
use App\Models\Transaction\Outbound\Job as outboundJob;

use App\Models\Transaction\Outbound\Despatch as outboundDespatch;

use App\Models\Transaction\Outbound\Order as outboundOrder;

use App\Models\Transaction\Stock\Transaction as StockTransaction;

class PickingController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $list_data = DB::table("iv_outbound_detail as a")
                ->select("a.*", "b.product_name", "c.site_name", "d.area_name")
                ->join("iv_product as b", "a.product_id", "b.id")
                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                ->where("a.company_id", $company_id)
                ->where("a.outbound_id", $request->outbound_id)
                ->where("a.picking_flag", "No")
                ->get();

            return datatables()->of($list_data)
                ->addColumn("check", function ($data) {
                    return "<input type='checkbox' required='required' name='picking_id[]' class='picking-check' id='" . $data->id . "' value='" . $data->id . "'>";
                })
                ->rawColumns(["check"])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function submit(Request $request)
    {

        $exception = DB::transaction(function () use ($request) {
            $company_id = Auth::user()->company_id;
            $confirmed_by = Auth::user()->username;
            $confirmed_date = \Carbon\Carbon::now();
            $created = \Carbon\Carbon::now();
            try {
                $data = $request->picking_id;

                foreach ($data as $id) {
                    $datapicking = DB::table("iv_outbound_detail as a")
                        ->select("a.*", "b.product_name", "c.site_name", "d.area_name", "b.pick_criteria", "b.shelf_life")
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->leftjoin("iv_site as c", "a.site_id", "c.id")
                        ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                        ->where("a.id", $id)
                        ->where("a.picking_flag", "No")
                        ->get();

                    $location_from = "";
                    $location_to = "zzzzzzzzzzzzzzz";
                    foreach ($datapicking as $value) {
                        $site = DB::table("iv_site as a")
                            ->join("iv_site_type as b", "a.type_id", "b.id")
                            ->where("a.id", $value->site_id)
                            ->first();

                        $job = DB::table("iv_outbound_job as a")
                            ->select("b.class_name", "a.branch_id")
                            ->join("iv_job_class as b", "a.class_id", "b.id")
                            ->where("a.id", $value->outbound_id)
                            ->first();

                        $branch_id = $job->branch_id;
                        $outbound_id = $value->outbound_id;
                        $actual_qty = $value->pqty;
                        $pick_criteria = $value->pick_criteria;
                        // auto correction
                        // dd($value);
                        // $dataCorrection = array('company_id' => $value->company_id, 'branch_id' => $job->branch_id, 'principal_id' => $value->principal_id, 'product_id' => $value->product_id);
                        // $this->autocorrection($request->picking_id, $dataCorrection);

                        $expired_flag = \Carbon\Carbon::today()->addDay($value->shelf_life);

                        if (!empty($value->location_from) && !empty($value->location_to)) {
                            $location_from = $value->location_from;
                            $location_to = $value->location_to;
                        } else {
                            if (!empty($value->location_from) && empty($value->location_to)) {
                                $location_from = $value->location_from;
                                $location_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                            } else if (empty($value->brand_code_from) && !empty($value->location_to)) {
                                $location_from = "";
                                $location_to = $value->location_to;
                            } else {
                                $location_from = "";
                                $location_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                            }
                        }

                        if (!empty($value->lot_no)) {
                            $lot_from = $value->lot_no;
                            $lot_to = $value->lot_no;
                        } else {
                            $lot_from = "";
                            $lot_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                        }

                        if (!empty($value->site_id)) {
                            $site_id_from = $value->site_id;
                            $site_id_to = $value->site_id;
                        } else {
                            $site_id_from = "";
                            $site_id_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                        }

                        if (!empty($value->area_id)) {
                            $area_id_from = $value->area_id;
                            $area_id_to = $value->area_id;
                        } else {
                            $area_id_from = "";
                            $area_id_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                        }

                        $summary_qty = 0;
                        while ($summary_qty < $actual_qty) {
                            if ($job->class_name != "Cross Dock") {
                                if (empty($value->site_id) || $value->site_id == null) {
                                    $status = "A";
                                } else {
                                    $site = DB::table("iv_site as a")
                                        ->join("iv_site_type as b", "a.type_id", "b.id")
                                        ->where("a.id", $value->site_id)
                                        ->first();

                                    if ($site->type_name == "Bulk") {
                                        $status = "B";
                                    } else {
                                        $status = "A";
                                    }
                                }
                                if ($status == "B") {
                                    $stock = stockLedger::from("iv_stock_ledger as a")
                                        ->select("a.*")
                                        ->join("iv_site as c", "a.site_id", "c.id")
                                        ->where("a.company_id", $value->company_id)
                                        ->where("a.branch_id", $branch_id)
                                        ->where("a.principal_id", $value->principal_id)
                                        ->where("a.product_id", $value->product_id)
                                        ->where("a.qtya", ">", 0)
                                        ->where("a.freeze_flag", "No")
                                        ->where("a.site_id", $value->site_id)
                                        ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                                        ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$lot_from, $lot_to])
                                        ->orderby("a.qtya", "desc")
                                        ->first();
                                } else {
                                    if ($pick_criteria == "FEFO") {
                                        $stock = stockLedger::from("iv_stock_ledger as a")
                                            ->select("a.*")
                                            ->leftjoin("iv_location as b", "a.location_id", "b.id")
                                            ->join("iv_site as c", "a.site_id", "c.id")
                                            ->where("c.type_id", 1)
                                            ->where("a.company_id", $value->company_id)
                                            ->where("a.branch_id", $branch_id)
                                            ->where("a.principal_id", $value->principal_id)
                                            ->where("a.product_id", $value->product_id)
                                            ->where("a.qtya", ">", 0)
                                            ->where("a.freeze_flag", "No")
                                            // ->where("a.exp_date", ">", $expired_flag)
                                            ->where("a.status", "G") // ambil status yang goods
                                            // ->whereIn("b.status_code", ['F', 'M', 'P'])
                                            ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                                            ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$lot_from, $lot_to])
                                            ->orderbyRaw("CASE WHEN b.status_code = 'B' THEN 3 WHEN b.status_code = 'P' THEN 2 ELSE 1 END asc")
                                            ->orderby("a.exp_date", "asc")
                                            ->orderby("a.qtya", "asc")
                                            ->orderby("a.site_id", "asc")
                                            ->orderby("a.area_id", "asc")
                                            ->orderby("a.location_code", "asc")
                                            ->first();
                                        // dd($stock);
                                    } elseif ($pick_criteria == "FIFO") {
                                        $stock =  stockLedger::from("iv_stock_ledger as a")
                                            ->select("a.*")
                                            ->leftjoin("iv_location as b", "a.location_id", "b.id")
                                            ->join("iv_site as c", "a.site_id", "c.id")
                                            ->where("c.type_id", 1)
                                            ->where("a.company_id", $value->company_id)
                                            ->where("a.branch_id", $branch_id)
                                            ->where("a.principal_id", $value->principal_id)
                                            ->where("a.product_id", $value->product_id)
                                            ->where("a.qtya", ">", 0)
                                            ->where("a.freeze_flag", "No")
                                            ->where("a.status", "G") // ambil status yang goods
                                            // ->whereIn("b.status_code", ['F', 'M', 'P'])
                                            ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                                            ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$lot_from, $lot_to])
                                            ->whereBetween(DB::raw("COALESCE(a.site_id, '')"), [$site_id_from, $site_id_to])
                                            ->whereBetween(DB::raw("COALESCE(a.area_id, '')"), [$area_id_from, $area_id_to])
                                            // diatas adalah updatenya
                                            ->orderbyRaw("CASE WHEN b.status_code = 'B' THEN 3 WHEN b.status_code = 'P' THEN 2 ELSE 1 END asc")
                                            ->orderby("a.job_date", "asc")
                                            ->orderby("a.qtya", "asc")
                                            // ->orderby("a.site_id", "asc")
                                            // ->orderby("a.area_id", "asc")
                                            // diatas terdapat bug, karena site_id di ambil secara ascending, mengabaikan parameter request yang sudah di buat. (kejadian case lebih sering ke faurecia yang memiliki 3 site)
                                            ->orderby("a.location_code", "asc")
                                            ->first();
                                    } elseif ($pick_criteria == "LEFO") {
                                        $stock =  stockLedger::from("iv_stock_ledger as a")
                                            ->select("a.*")
                                            ->leftjoin("iv_location as b", "a.location_id", "b.id")
                                            ->join("iv_site as c", "a.site_id", "c.id")
                                            ->where("c.type_id", 1)
                                            ->where("a.company_id", $value->company_id)
                                            ->where("a.branch_id", $branch_id)
                                            ->where("a.principal_id", $value->principal_id)
                                            ->where("a.product_id", $value->product_id)
                                            ->where("a.qtya", ">", 0)
                                            ->where("a.freeze_flag", "No")
                                            ->where("a.exp_date", ">", $expired_flag)
                                            ->where("a.status", "G") // ambil status yang goods
                                            // ->whereIn("b.status_code", ['F', 'M', 'P'])
                                            ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                                            ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$lot_from, $lot_to])
                                            ->orderbyRaw("CASE WHEN b.status_code = 'B' THEN 3 WHEN b.status_code = 'P' THEN 2 ELSE 1 END asc")
                                            ->orderby("a.exp_date", "desc")
                                            ->orderby("a.qtya", "asc")
                                            ->orderby("a.site_id", "asc")
                                            ->orderby("a.area_id", "asc")
                                            ->orderby("a.location_code", "asc")
                                            ->first();
                                    } elseif ($pick_criteria == "LIFO") {
                                        $stock =  stockLedger::from("iv_stock_ledger as a")
                                            ->select("a.*")
                                            ->leftjoin("iv_location as b", "a.location_id", "b.id")
                                            ->join("iv_site as c", "a.site_id", "c.id")
                                            ->where("c.type_id", 1)
                                            ->where("a.company_id", $value->company_id)
                                            ->where("a.branch_id", $branch_id)
                                            ->where("a.principal_id", $value->principal_id)
                                            ->where("a.product_id", $value->product_id)
                                            ->where("a.qtya", ">", 0)
                                            ->where("a.freeze_flag", "No")
                                            ->where("a.status", "G") // ambil status yang goods
                                            // ->whereIn("b.status_code", ['F', 'M', 'P'])
                                            ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                                            ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$lot_from, $lot_to])
                                            ->orderbyRaw("CASE WHEN b.status_code = 'B' THEN 3 WHEN b.status_code = 'P' THEN 2 ELSE 1 END asc")
                                            ->orderby("a.job_date", "desc")
                                            ->orderby("a.qtya", "asc")
                                            ->orderby("a.site_id", "asc")
                                            ->orderby("a.area_id", "asc")
                                            ->orderby("a.location_code", "asc")
                                            ->first();
                                    } elseif ($pick_criteria == "FMFO") {
                                        $stock =  stockLedger::from("iv_stock_ledger as a")
                                            ->select("a.*")
                                            ->leftjoin("iv_location as b", "a.location_id", "b.id")
                                            ->join("iv_site as c", "a.site_id", "c.id")
                                            ->where("c.type_id", 1)
                                            ->where("a.company_id", $value->company_id)
                                            ->where("a.branch_id", $branch_id)
                                            ->where("a.principal_id", $value->principal_id)
                                            ->where("a.product_id", $value->product_id)
                                            ->where("a.qtya", ">", 0)
                                            ->where("a.freeze_flag", "No")
                                            ->where("a.mfg_date", ">", $expired_flag)
                                            ->where("a.status", "G") // ambil status yang goods
                                            // ->whereIn("b.status_code", ['F', 'M', 'P'])
                                            ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                                            ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$lot_from, $lot_to])
                                            ->orderbyRaw("CASE WHEN b.status_code = 'B' THEN 3 WHEN b.status_code = 'P' THEN 2 ELSE 1 END asc")
                                            ->orderby("a.mfg_date", "asc")
                                            ->orderby("a.qtya", "asc")
                                            ->orderby("a.site_id", "asc")
                                            ->orderby("a.area_id", "asc")
                                            ->orderby("a.location_code", "asc")
                                            ->first();
                                    } elseif ($pick_criteria == "BATCH") {
                                        $stock =  stockLedger::from("iv_stock_ledger as a")
                                            ->select("a.*")
                                            ->leftjoin("iv_location as b", "a.location_id", "b.id")
                                            ->join("iv_site as c", "a.site_id", "c.id")
                                            ->where("c.type_id", 1)
                                            ->where("a.company_id", $value->company_id)
                                            ->where("a.branch_id", $branch_id)
                                            ->where("a.principal_id", $value->principal_id)
                                            ->where("a.product_id", $value->product_id)
                                            ->where("a.qtya", ">", 0)
                                            ->where("a.freeze_flag", "No")
                                            ->where("a.status", "G") // ambil status yang goods
                                            // ->whereIn("b.status_code", ['F', 'M', 'P'])
                                            ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                                            ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$lot_from, $lot_to])
                                            ->orderbyRaw("CASE WHEN b.status_code = 'B' THEN 3 WHEN b.status_code = 'P' THEN 2 ELSE 1 END asc")
                                            ->orderby("a.lot_no", "asc")
                                            ->orderby("a.qtya", "asc")
                                            ->orderby("a.site_id", "asc")
                                            ->orderby("a.area_id", "asc")
                                            ->orderby("a.location_code", "asc")
                                            ->first();
                                    } elseif ($pick_criteria == "DOCREF") {
                                        $stock =  stockLedger::from("iv_stock_ledger as a")
                                            ->select("a.*")
                                            ->leftjoin("iv_location as b", "a.location_id", "b.id")
                                            ->join("iv_site as c", "a.site_id", "c.id")
                                            ->where("c.type_id", 1)
                                            ->where("a.company_id", $value->company_id)
                                            ->where("a.branch_id", $branch_id)
                                            ->where("a.principal_id", $value->principal_id)
                                            ->where("a.product_id", $value->product_id)
                                            ->where("a.qtya", ">", 0)
                                            ->where("a.freeze_flag", "No")
                                            // ->whereIn("b.status_code", ['F', 'M', 'P'])
                                            ->where("a.status", "G") // ambil status yang goods
                                            ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                                            ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$lot_from, $lot_to])
                                            ->orderbyRaw("CASE WHEN b.status_code = 'B' THEN 3 WHEN b.status_code = 'P' THEN 2 ELSE 1 END asc")
                                            ->orderby("a.document_ref", "asc")
                                            ->orderby("a.qtya", "asc")
                                            ->orderby("a.site_id", "asc")
                                            ->orderby("a.area_id", "asc")
                                            ->orderby("a.location_code", "asc")
                                            ->first();
                                    }
                                }
                            } else {
                                $stock = stockLedger::from("iv_stock_ledger as a")
                                    ->select("a.*")
                                    ->join("iv_site as c", "a.site_id", "c.id")
                                    ->where("a.company_id", $value->company_id)
                                    ->where("a.branch_id", $branch_id)
                                    ->where("a.principal_id", $value->principal_id)
                                    ->where("a.product_id", $value->product_id)
                                    ->where("a.qtya", ">", 0)
                                    ->where("a.freeze_flag", "No")
                                    ->where("a.site_id", 8)
                                    ->where("a.status", "G") // ambil status yang goods
                                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                                    ->whereBetween(DB::raw("COALESCE(a.lot_no, '')"), [$lot_from, $lot_to])
                                    ->orderby("a.qtya", "desc")
                                    ->first();
                            }

                            if (!isset($stock) || empty($stock)) {
                                DB::rollBack();
                                $message = ["error" => "Quantity not available. SKU Code : $value->product_code Quantity : $actual_qty"];
                                return $message;
                            }

                            $pallet_qty = $stock->qtya;

                            $summary_qty = $summary_qty + $pallet_qty;

                            if ($summary_qty <= $actual_qty) {
                                $qty = $pallet_qty;
                            } else {
                                $summary_qty = $summary_qty - $pallet_qty;
                                $qty = $actual_qty - $summary_qty;
                                $summary_qty = $summary_qty + $qty;
                            }

                            if ($stock->qtya < $qty) {
                                DB::rollBack();
                                $message = ["error" => "Quantity not enough."];

                                return $message;
                            }

                            $stock->qtya = $stock->qtya - $qty;
                            $stock->qtyp = $stock->qtyp + $qty;
                            $stock->save();

                            $pqty = ($qty  - ($qty % $stock->uppp)) / $stock->uppp;
                            $mqty = (($qty % $stock->uppp) - (($qty % $stock->uppp) % $stock->muppp)) / $stock->muppp;
                            $bqty = $qty % $stock->uppp % $stock->muppp;

                            $outbound_batch = [];

                            $outbound_batch[] = [
                                "outbound_id" => $value->outbound_id,
                                "picking_id" => $value->id,
                                "serial_id" => $stock->id,
                                "company_id" => $value->company_id,
                                "principal_id" => $value->principal_id,
                                "customer_id" => $value->customer_id,
                                "order_no" => $value->order_no,
                                "serial_no" => $stock->serial_no,
                                "job_no" => $value->job_no,
                                "product_id" => $stock->product_id,
                                "product_code" => $stock->product_code,
                                "po_number" => $stock->po_number,
                                "lot_no" => $stock->lot_no,
                                "document_ref" => $stock->document_ref,
                                "reference_no" => $value->document_ref,
                                "mfg_date" => $stock->mfg_date,
                                "exp_date" => $stock->exp_date,
                                "site_id" => $stock->site_id,
                                "area_id" => $stock->area_id,
                                "location_id" => $stock->location_id,
                                "location_code" => $stock->location_code,
                                "puom" => $stock->puom,
                                "muom" => $stock->muom,
                                "buom" => $stock->buom,
                                "uppp" => $stock->uppp,
                                "muppp" => $stock->muppp,
                                "pqty" => $pqty,
                                "mqty" => $mqty,
                                "bqty" => $bqty,
                                "qty" => $qty,
                                "pallet_qty" => $stock->pallet_qty,
                                "base_unit" => $stock->base_unit,
                                "created_at" => $created
                            ];

                            outboundBatch::insert($outbound_batch);
                        }

                        $detail = outboundDetails::find($id);

                        $detail->picking_flag = "Yes";
                        $detail->picking_by = $confirmed_by;
                        $detail->picking_date = $confirmed_date;
                        $detail->save();
                    }
                }

                outboundDespatch::where("outbound_id", $outbound_id)->delete();

                $batch = DB::table('iv_outbound_batch as a')
                    ->select(DB::raw("sum(a.pqty) as pqty"), DB::raw("sum(a.mqty) as mqty"), DB::raw("sum(a.bqty) as bqty"), "a.company_id", "a.principal_id", "a.outbound_id", "a.job_no", "a.customer_id", "b.mode_id", "c.store_id", "a.reference_no")
                    ->join("iv_outbound_job as b", "a.outbound_id", "b.id")
                    ->join("iv_customer as c", "a.customer_id", "c.id")
                    ->where("a.outbound_id", $outbound_id)
                    ->groupby("a.company_id", "a.principal_id", "a.outbound_id", "a.job_no", "a.customer_id", "b.mode_id", "c.store_id", "a.reference_no")
                    ->get();

                $despatch = [];
                foreach ($batch as $value) {
                    $job_date = \Carbon\Carbon::today();

                    $year = $job_date->year;
                    $tahun = $job_date->format('y');
                    $month = $job_date->month;

                    $job = outboundDespatch::where('company_id', $company_id)
                        ->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->max("do_no");

                    if (is_null($job)) {
                        $increment = 1;
                    } else {
                        $increment = substr($job, 4, 4) + 1;
                    }

                    $do_no = $tahun . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

                    $order = outboundOrder::where("outbound_id", $outbound_id)
                        ->where("customer_id", $value->customer_id)
                        ->where("confirmed_flag", "Yes")
                        ->get()
                        ->count();

                    $job = outboundJob::find($outbound_id);

                    $job->allocated_flag = 'Yes';
                    $job->allocated_by = $confirmed_by;
                    $job->allocated_date = \Carbon\Carbon::now();
                    $job->save();

                    $despatch[] = [
                        "company_id" => $value->company_id,
                        "principal_id" => $value->principal_id,
                        "outbound_id" => $value->outbound_id,
                        "job_no" => $value->job_no,
                        "do_no" => $do_no,
                        "customer_id" => $value->customer_id,
                        "reference_no" => $value->reference_no,
                        "mode_id" => $value->mode_id,
                        "pqty" => $value->pqty,
                        "mqty" => $value->mqty,
                        "bqty" => $value->bqty,
                        "order_count" => $order,
                        "etd" => $job->etd,
                        "created_at" => $created
                    ];
                }

                outboundDespatch::insert($despatch);

                DB::commit();

                $message = ["success" => "Data successfully processed."];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    private function autocorrection($pickingId, $data)
    {
        $serial = stockLedger::WHERE('company_id', $data['company_id'])
            ->WHERE('branch_id', $data['branch_id'])
            ->WHERE('principal_id', $data['principal_id'])
            ->WHERE('product_id', $data['product_id'])
            ->get();
        $qtyCorrection = array();
        foreach ($serial as $key => $ledgerdata) {
            $ledger = stockLedger::find($ledgerdata->id);
            if ($ledger->qtys != ($ledger->qtyp + $ledger->qtya)) {
                $selisih = 0;
                if ($ledger->qtys > ($ledger->qtyp + $ledger->qtya)) {
                    $selisih = $ledger->qtys - ($ledger->qtyp + $ledger->qtya);
                    $ledger->qtya = $ledger->qtya + $selisih;
                    $ledger->save();
                } else if ($ledger->qtys < ($ledger->qtyp + $ledger->qtya)) {
                    $selisih = ($ledger->qtyp + $ledger->qtya) - $ledger->qtys;
                    $ledger->qtys = $ledger->qtys + $selisih;
                    $ledger->save();
                }
            }
            $stockPerLocation = DB::select("CALL sp_stock_per_location_transaction(?,?,?,?,?)", array($ledger->company_id, $ledger->branch_id, $ledger->principal_id, $ledger->product_id, $ledger->location_id));
            // dd($stockPerLocation);
            $stockPerLocation = $stockPerLocation[0];

            if ($ledger->qtys != $stockPerLocation->qty) {
                $data_process = $ledger;
                $selisih = $stockPerLocation->qty - $data_process->qtys;
                $correction_remark = '';
                $action_remark = '';

                if ($ledger->qtya + $selisih < 0) {
                    $correction_remark = 'gagal update karena qty actual hasil pengurangan < 0';
                    $action_remark = "gagal koreksi data";
                } else if ($stockPerLocation->qty < 0) {
                    $correction_remark = 'gagal update karena qty transaction < 0';
                    $action_remark = "gagal koreksi data";
                } else if ($ledger->qtys + $selisih < 0) {
                    $correction_remark = 'gagal update karena qty onhand hasil pengurangan < 0';
                    $action_remark = "gagal koreksi data";
                } else if ($ledger->qtya >= 0) {
                    $ledger->qtya = $ledger->qtya + $selisih;
                    $ledger->qtys = $stockPerLocation->qty;
                    $ledger->save();
                    $action_remark = "Adjust Ledger OnHand $selisih";
                }

                DB::table('iv_stock_auto_adjustment_log')->insert([
                    'branch_id' => "$data_process->branch_id",
                    'company_id' => "$data_process->company_id",
                    'principal_id' => "$data_process->principal_id",
                    'product_id' => "$data_process->product_id",
                    'product_code' => "$stockPerLocation->product_code",
                    'lot_no' => "$data_process->lot_no",
                    'mfg_date' => "$data_process->mfg_date",
                    'exp_date' => "$data_process->exp_date",
                    'ledger_onhand' => "$data_process->qtys",
                    'ledger_booking' => "$data_process->qtyp",
                    'ledger_available' => "$data_process->qtya",
                    'transaction_onhand' => "$stockPerLocation->qty",
                    'variance' => "$selisih",
                    'site_id' => "$data_process->site_id",
                    'area_id' => "$data_process->area_id",
                    'location_id' => "$data_process->location_id",
                    'action' => "$action_remark",
                    'correction_date' => \Carbon\Carbon::now(),
                    'correction_remark' => "$correction_remark"
                ]);
            }
        }
        // DB::commit();
        return true;
    }
}
