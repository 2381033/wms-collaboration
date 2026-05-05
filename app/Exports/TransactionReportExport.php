<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class TransactionReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStrictNullComparison
{
    protected $group_on = null;
    protected $jobType = null;
    protected $branch_id = null;
    protected $principal = null;
    protected $product_from = null;
    protected $product_to = null;
    protected $date_from = null;
    protected $date_to = null;
    protected $site_id = null;
    protected $area_id = null;
    protected $location_from = null;
    protected $location_to = null;

    public function __construct($group_on, $jobType, $branch_id, $principal, $product_from, $product_to, $date_from, $date_to, $site_id, $area_id, $location_from, $location_to)
    {
        $this->group_on = $group_on;
        $this->jobType = $jobType;
        $this->branch_id = $branch_id;
        $this->principal = $principal;
        $this->product_from = $product_from;
        $this->product_to = $product_to;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->site_id = $site_id;
        $this->area_id = $area_id;
        $this->location_from = $location_from;
        $this->location_to = $location_to;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $company_id = Auth::user()->company_id;
        $principal = \App\Models\Master\Principal::find($this->principal);

        switch ($this->jobType) {
            case 'All':
                $job_type = [
                    "IMP",
                    "EXP",
                    // "TFRI",
                    // "TFRO",
                    "ADJ+",
                    "ADJ-"
                ];
                break;
            case 'Inbound':
                $job_type = [
                    "IMP"
                ];
                break;
            case 'Outbound':
                $job_type = [
                    "EXP"
                ];
                break;
            case 'Moves':
                $job_type = [
                    "TFRI",
                    "TFRO",
                ];
                break;
            case 'Adjustments':
                $job_type = [
                    "ADJ+",
                    "ADJ-"
                ];
                break;

            default:
                # code...
                break;
        }

        $stockBefore = DB::table('iv_stock_transaction as a')
            ->select(
                'a.product_id',
                DB::raw("sum(CASE WHEN a.job_type IN ('IMP', 'TFRI', 'ADJ+') THEN a.qty ELSE 0 END ) as qty_received"),
                DB::raw("sum(CASE WHEN a.job_type IN ('EXP', 'TFRO', 'ADJ-') THEN a.qty ELSE 0 END ) as qty_issue")
            )
            ->join('iv_product as b', 'a.product_id', 'b.id')
            ->where('a.company_id', $company_id)
            ->where('a.principal_id', $this->principal)
            ->where("a.branch_id", $this->branch_id)
            ->where('a.job_date', '<', date($this->date_from))
            ->whereBetween('b.product_code', [$this->product_from, $this->product_to])
            ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $this->site_id)
            ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $this->area_id)
            ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$this->location_from, $this->location_to])
            ->groupBy('a.product_id')
            ->get();



        switch ($this->group_on) {
            case 'product':
                $stockList = DB::table('iv_stock_transaction as a')
                    ->select(
                        'a.job_no',
                        'a.job_date',
                        'a.job_type',
                        'a.product_id',
                        'a.product_code',
                        'b.product_name',
                        'a.lot_no',
                        'a.document_ref',
                        'a.mfg_date',
                        'a.exp_date',
                        'c.site_name',
                        'd.area_name',
                        'a.location_code',
                        'b.uppp',
                        'b.muppp',
                        'a.qty',
                        'b.puom',
                        'b.muom',
                        'b.buom',
                        'a.pqty',
                        'a.mqty',
                        'a.bqty',
                        'a.qty',
                        'b.gross_weight',
                        'b.volume',
                        'a.created_at',
                        "a.reference_no",
                        DB::raw("CASE WHEN a.job_type = 'IMP' THEN e.description WHEN a.job_type = 'EXP' THEN g.description ELSE '' END as description")
                    )
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->leftjoin('iv_site as c', 'a.site_id', 'c.id')
                    ->leftjoin('iv_site_area as d', 'a.area_id', 'd.id')
                    ->leftjoin('iv_inbound_job as e', function ($data) {
                        $data->on("a.principal_id", "e.principal_id")
                            ->on("a.reference_no", "e.job_no");
                    })
                    ->leftjoin('iv_outbound_job as g', function ($data) {
                        $data->on("a.principal_id", "g.principal_id")
                            ->on("a.reference_no", "g.job_no");
                    })
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $this->principal)
                    ->where("a.branch_id", $this->branch_id)
                    ->whereBetween('b.product_code', [$this->product_from, $this->product_to])
                    ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $this->site_id)
                    ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $this->area_id)
                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$this->location_from, $this->location_to])
                    ->whereBetween('a.job_date', [date($this->date_from), date($this->date_to)])
                    ->whereIn('a.job_type', $job_type)
                    ->orderBy('b.product_code', 'asc')
                    ->orderBy('b.product_name', 'asc')
                    ->orderBy('a.job_date', 'asc')
                    ->orderBy(DB::raw("CASE WHEN a.job_type = 'IMP' THEN 1 WHEN a.job_type = 'TFRI' THEN 2 WHEN a.job_type = 'ADJ+' THEN 3 WHEN a.job_type = 'EXP' THEN 4 WHEN a.job_type = 'TFRO' THEN 5 ELSE 6 END"), 'asc')
                    // ->distinct()
                    ->get();

                $first_line = true;
                $product_before = "";
                $list = [];
                foreach ($stockList as $value) {
                    if ($product_before !== $value->product_id) {
                        $stock = $stockBefore->where('product_id', $value->product_id)->first();

                        $job_date = \Carbon\Carbon::parse($this->date_from)->format("d/m/Y");

                        if (isset($stock)) {
                            $qty_open = $stock->qty_received - $stock->qty_issue;

                            if ($principal->multi_level == "Yes") {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "trx_qty2" => 0,
                                    "trx_qty3" => 0,
                                    "bal_qty1" => ($qty_open  - ($qty_open % $value->uppp)) / $value->uppp,
                                    "bal_qty2" => (($qty_open % $value->uppp) - (($qty_open % $value->uppp) % $value->muppp)) / $value->muppp,
                                    "bal_qty3" => $qty_open % $value->uppp % $value->muppp,
                                    "puom" => $value->puom,
                                    "muom" => $value->muom,
                                    "buom" => $value->buom,
                                    "gross_weight" => $qty_open * $value->gross_weight,
                                    "volume" => $qty_open * $value->volume,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null,
                                ];
                            } else {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "bal_qty1" => ($qty_open  - ($qty_open % $value->uppp)) / $value->uppp,
                                    "puom" => $value->puom,
                                    "gross_weight" => $qty_open * $value->gross_weight,
                                    "volume" => $qty_open * $value->volume,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null
                                ];
                            }
                        } else {
                            if ($principal->multi_level == "Yes") {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "trx_qty2" => 0,
                                    "trx_qty3" => 0,
                                    "bal_qty1" => 0,
                                    "bal_qty2" => 0,
                                    "bal_qty3" => 0,
                                    "puom" => $value->puom,
                                    "muom" => $value->muom,
                                    "buom" => $value->buom,
                                    "gross_weight" => 0,
                                    "volume" => 0,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null
                                ];
                            } else {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "bal_qty1" => 0,
                                    "puom" => $value->puom,
                                    "gross_weight" => 0,
                                    "volume" => 0,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null
                                ];
                            }

                            $qty_open = 0;
                        }
                    }

                    if ($product_before != $value->product_id) {
                        $balance = $qty_open;
                    }

                    $kali = 1;
                    if ($value->job_type == 'EXP' || $value->job_type == 'TFRO' || $value->job_type == 'ADJ-') {
                        $kali = -1;
                    }

                    $qty = $kali * $value->qty;
                    $balance = $balance + $qty;

                    switch ($value->job_type) {
                        case 'IMP':
                            $job_desc = 'Inbound';
                            break;
                        case 'TFRI':
                            $job_desc = 'Transfer In';
                            break;
                        case 'ADJ+':
                            $job_desc = 'Adj. Plus';
                            break;
                        case 'EXP':
                            $job_desc = 'Outbound';
                            break;
                        case 'TFRO':
                            $job_desc = 'Transfer Out';
                            break;
                        case 'ADJ-':
                            $job_desc = 'Adj. Minus';
                            break;

                        default:
                            $job_desc = '';
                            break;
                    }

                    if ($principal->multi_level == "Yes") {
                        $list[] = [
                            "job_no" => $value->job_no,
                            "job_date" => $value->job_date,
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "job_type" => $job_desc,
                            "trx_qty1" => $value->pqty,
                            "trx_qty2" => $value->mqty,
                            "trx_qty3" => $value->bqty,
                            "bal_qty1" => ($balance  - ($balance % $value->uppp)) / $value->uppp,
                            "bal_qty2" => (($balance % $value->uppp) - (($balance % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bal_qty3" => $balance % $value->uppp % $value->muppp,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom,
                            "gross_weight" => $value->qty * $value->gross_weight,
                            "volume" => $value->qty * $value->volume,
                            "created_at" => $value->created_at,
                            "description" => $value->description ?? '',
                            "order_no" => $value->order_no ?? '',
                            "reference_no" => $value->reference_no,
                        ];
                    } else {
                        $list[] = [
                            "job_no" => $value->job_no,
                            "job_date" => $value->job_date,
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "job_type" => $job_desc,
                            "trx_qty1" => $value->pqty,
                            "bal_qty1" => ($balance  - ($balance % $value->uppp)) / $value->uppp,
                            "puom" => $value->puom,
                            "gross_weight" => $value->qty * $value->gross_weight,
                            "volume" => $value->qty * $value->volume,
                            "created_at" => $value->created_at,
                            "description" => $value->description,
                            // "order_no" => $value->order_no,
                            "reference_no" => $value->reference_no
                        ];
                    }

                    $product_before = $value->product_id;

                    if ($first_line) {
                        $first_line = false;
                    }
                }
                return new Collection($list);
                break;
            case 'product-lot':
                $stockList = DB::table('iv_stock_transaction as a')
                    ->select(
                        'a.job_no',
                        'a.job_date',
                        'a.job_type',
                        'a.product_id',
                        'a.product_code',
                        'b.product_name',
                        'a.lot_no',
                        'a.document_ref',
                        'a.mfg_date',
                        'a.exp_date',
                        'c.site_name',
                        'd.area_name',
                        'a.location_code',
                        'b.uppp',
                        'b.muppp',
                        'a.qty',
                        'b.puom',
                        'b.muom',
                        'b.buom',
                        'a.pqty',
                        'a.mqty',
                        'a.bqty',
                        'a.qty',
                        'b.gross_weight',
                        'b.volume',
                        "a.created_at",
                        "f.order_no",
                        "a.reference_no",
                        DB::raw("CASE WHEN a.job_type = 'IMP' THEN e.description WHEN a.job_type = 'EXP' THEN g.description ELSE '' END as description")
                    )
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->leftjoin('iv_site as c', 'a.site_id', 'c.id')
                    ->leftjoin('iv_site_area as d', 'a.area_id', 'd.id')
                    ->leftjoin('iv_inbound_job as e', function ($data) {
                        $data->on("a.principal_id", "e.principal_id")
                            ->on("a.reference_no", "e.job_no");
                    })
                    ->leftjoin('iv_outbound_order as f', function ($data) {
                        $data->on("a.principal_id", "f.principal_id")
                            ->on("a.reference_no", "f.job_no");
                    })
                    ->leftjoin('iv_outbound_job as g', function ($data) {
                        $data->on("a.principal_id", "g.principal_id")
                            ->on("a.reference_no", "g.job_no");
                    })
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $this->principal)
                    ->where("a.branch_id", $this->branch_id)
                    ->whereBetween('b.product_code', [$this->product_from, $this->product_to])
                    ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $this->site_id)
                    ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $this->area_id)
                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$this->location_from, $this->location_to])
                    ->whereBetween('a.job_date', [date($this->date_from), date($this->date_to)])
                    ->whereIn('a.job_type', $job_type)
                    ->orderBy('b.product_name', 'asc')
                    ->orderBy('a.lot_no', 'asc')
                    ->orderBy('a.job_date', 'asc')
                    ->orderBy(DB::raw("CASE WHEN a.job_type = 'IMP' THEN 1 WHEN a.job_type = 'TFRI' THEN 2 WHEN a.job_type = 'ADJ+' THEN 3 WHEN a.job_type = 'EXP' THEN 4 WHEN a.job_type = 'TFRO' THEN 5 ELSE 6 END"), 'asc')
                    ->distinct()->get();

                $first_line = true;
                $product_before = "";
                $document_before = "";
                $list = [];
                foreach ($stockList as $value) {
                    if ($product_before !== $value->product_id || $document_before !== $value->lot_no) {
                        $stock = $stockBefore->where('product_id', $value->product_id)
                            ->where('lot_no', $value->lot_no)
                            ->first();

                        $job_date = \Carbon\Carbon::parse($this->date_from)->format("d/m/Y");

                        if (isset($stock)) {
                            $qty_open = $stock->qty_received - $stock->qty_issue;

                            if ($principal->multi_level == "Yes") {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "trx_qty2" => 0,
                                    "trx_qty3" => 0,
                                    "bal_qty1" => ($qty_open  - ($qty_open % $value->uppp)) / $value->uppp,
                                    "bal_qty2" => (($qty_open % $value->uppp) - (($qty_open % $value->uppp) % $value->muppp)) / $value->muppp,
                                    "bal_qty3" => $qty_open % $value->uppp % $value->muppp,
                                    "puom" => $value->puom,
                                    "muom" => $value->muom,
                                    "buom" => $value->buom,
                                    "gross_weight" => $qty_open * $value->gross_weight,
                                    "volume" => $qty_open * $value->volume,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null,
                                ];
                            } else {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "bal_qty1" => ($qty_open  - ($qty_open % $value->uppp)) / $value->uppp,
                                    "puom" => $value->puom,
                                    "gross_weight" => $qty_open * $value->gross_weight,
                                    "volume" => $qty_open * $value->volume,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null
                                ];
                            }
                        } else {
                            if ($principal->multi_level == "Yes") {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "trx_qty2" => 0,
                                    "trx_qty3" => 0,
                                    "bal_qty1" => 0,
                                    "bal_qty2" => 0,
                                    "bal_qty3" => 0,
                                    "puom" => $value->puom,
                                    "muom" => $value->muom,
                                    "buom" => $value->buom,
                                    "gross_weight" => 0,
                                    "volume" => 0,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null
                                ];
                            } else {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "bal_qty1" => 0,
                                    "puom" => $value->puom,
                                    "gross_weight" => 0,
                                    "volume" => 0,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null
                                ];
                            }

                            $qty_open = 0;
                        }
                    }

                    if ($product_before != $value->product_id || $document_before !== $value->lot_no) {
                        $balance = $qty_open;
                    }

                    $kali = 1;
                    if ($value->job_type == 'EXP' || $value->job_type == 'TFRO' || $value->job_type == 'ADJ-') {
                        $kali = -1;
                    }

                    $qty = $kali * $value->qty;
                    $balance = $balance + $qty;

                    switch ($value->job_type) {
                        case 'IMP':
                            $job_desc = 'Inbound';
                            break;
                        case 'TFRI':
                            $job_desc = 'Transfer In';
                            break;
                        case 'ADJ+':
                            $job_desc = 'Adj. Plus';
                            break;
                        case 'EXP':
                            $job_desc = 'Outbound';
                            break;
                        case 'TFRO':
                            $job_desc = 'Transfer Out';
                            break;
                        case 'ADJ-':
                            $job_desc = 'Adj. Minus';
                            break;

                        default:
                            $job_desc = '';
                            break;
                    }

                    if ($principal->multi_level == "Yes") {
                        $list[] = [
                            "job_no" => $value->job_no,
                            "job_date" => $value->job_date,
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "job_type" => $job_desc,
                            "trx_qty1" => $value->pqty,
                            "trx_qty2" => $value->mqty,
                            "trx_qty3" => $value->bqty,
                            "bal_qty1" => ($balance  - ($balance % $value->uppp)) / $value->uppp,
                            "bal_qty2" => (($balance % $value->uppp) - (($balance % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bal_qty3" => $balance % $value->uppp % $value->muppp,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom,
                            "gross_weight" => $value->qty * $value->gross_weight,
                            "volume" => $value->qty * $value->volume,
                            "created_at" => $value->created_at,
                            "description" => $value->description,
                            "order_no" => $value->order_no,
                            "reference_no" => $value->reference_no,
                        ];
                    } else {
                        $list[] = [
                            "job_no" => $value->job_no,
                            "job_date" => $value->job_date,
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "job_type" => $job_desc,
                            "trx_qty1" => $value->pqty,
                            "bal_qty1" => ($balance  - ($balance % $value->uppp)) / $value->uppp,
                            "puom" => $value->puom,
                            "gross_weight" => $value->qty * $value->gross_weight,
                            "volume" => $value->qty * $value->volume,
                            "created_at" => $value->created_at,
                            "description" => $value->description,
                            "order_no" => $value->order_no,
                            "reference_no" => $value->reference_no,
                        ];
                    }

                    $product_before = $value->product_id;
                    $document_before = $value->lot_no;

                    if ($first_line) {
                        $first_line = false;
                    }
                }

                return new Collection($list);
                break;
            case 'product-doc':
                $stockList = DB::table('iv_stock_transaction as a')
                    ->select(
                        'a.job_no',
                        'a.job_date',
                        'a.job_type',
                        'a.product_id',
                        'a.product_code',
                        'b.product_name',
                        'a.lot_no',
                        'a.document_ref',
                        'a.mfg_date',
                        'a.exp_date',
                        'c.site_name',
                        'd.area_name',
                        'a.location_code',
                        'b.uppp',
                        'b.muppp',
                        'a.qty',
                        'b.puom',
                        'b.muom',
                        'b.buom',
                        'a.pqty',
                        'a.mqty',
                        'a.bqty',
                        'a.qty',
                        'b.gross_weight',
                        'b.volume',
                        "a.created_at",
                        "f.order_no",
                        "a.reference_no",
                        DB::raw("CASE WHEN a.job_type = 'IMP' THEN e.description WHEN a.job_type = 'EXP' THEN g.description ELSE '' END as description")
                    )
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->leftjoin('iv_site as c', 'a.site_id', 'c.id')
                    ->leftjoin('iv_site_area as d', 'a.area_id', 'd.id')
                    ->leftjoin('iv_inbound_job as e', function ($data) {
                        $data->on("a.principal_id", "e.principal_id")
                            ->on("a.reference_no", "e.job_no");
                    })
                    ->leftjoin('iv_outbound_order as f', function ($data) {
                        $data->on("a.principal_id", "f.principal_id")
                            ->on("a.reference_no", "f.job_no");
                    })
                    ->leftjoin('iv_outbound_job as g', function ($data) {
                        $data->on("a.principal_id", "g.principal_id")
                            ->on("a.reference_no", "g.job_no");
                    })
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $this->principal)
                    ->where("a.branch_id", $this->branch_id)
                    ->whereBetween('b.product_code', [$this->product_from, $this->product_to])
                    ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $this->site_id)
                    ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $this->area_id)
                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$this->location_from, $this->location_to])
                    ->whereBetween('a.job_date', [date($this->date_from), date($this->date_to)])
                    ->whereIn('a.job_type', $job_type)
                    ->orderBy('b.product_name', 'asc')
                    ->orderBy('a.document_ref', 'asc')
                    ->orderBy('a.job_date', 'asc')
                    ->orderBy(DB::raw("CASE WHEN a.job_type = 'IMP' THEN 1 WHEN a.job_type = 'TFRI' THEN 2 WHEN a.job_type = 'ADJ+' THEN 3 WHEN a.job_type = 'EXP' THEN 4 WHEN a.job_type = 'TFRO' THEN 5 ELSE 6 END"), 'asc')
                    ->distinct()->get();

                $first_line = true;
                $product_before = "";
                $document_before = "";
                $list = [];
                foreach ($stockList as $value) {
                    if ($product_before != $value->product_id || $document_before !== $value->document_ref) {
                        $stock = $stockBefore->where('product_id', $value->product_id)
                            ->where('document_ref', $value->document_ref)
                            ->first();

                        $job_date = \Carbon\Carbon::parse($this->date_from)->format("d/m/Y");

                        if (isset($stock)) {
                            $qty_open = $stock->qty_received - $stock->qty_issue;

                            if ($principal->multi_level == "Yes") {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "trx_qty2" => 0,
                                    "trx_qty3" => 0,
                                    "bal_qty1" => ($qty_open  - ($qty_open % $value->uppp)) / $value->uppp,
                                    "bal_qty2" => (($qty_open % $value->uppp) - (($qty_open % $value->uppp) % $value->muppp)) / $value->muppp,
                                    "bal_qty3" => $qty_open % $value->uppp % $value->muppp,
                                    "puom" => $value->puom,
                                    "muom" => $value->muom,
                                    "buom" => $value->buom,
                                    "gross_weight" => $qty_open * $value->gross_weight,
                                    "volume" => $qty_open * $value->volume,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null
                                ];
                            } else {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "bal_qty1" => ($qty_open  - ($qty_open % $value->uppp)) / $value->uppp,
                                    "puom" => $value->puom,
                                    "gross_weight" => $qty_open * $value->gross_weight,
                                    "volume" => $qty_open * $value->volume,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null
                                ];
                            }
                        } else {
                            if ($principal->multi_level == "Yes") {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "trx_qty2" => 0,
                                    "trx_qty3" => 0,
                                    "bal_qty1" => 0,
                                    "bal_qty2" => 0,
                                    "bal_qty3" => 0,
                                    "puom" => $value->puom,
                                    "muom" => $value->muom,
                                    "buom" => $value->buom,
                                    "gross_weight" => 0,
                                    "volume" => 0,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null
                                ];
                            } else {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "bal_qty1" => 0,
                                    "puom" => $value->puom,
                                    "gross_weight" => 0,
                                    "volume" => 0,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null
                                ];
                            }

                            $qty_open = 0;
                        }
                    }

                    if ($product_before != $value->product_id || $document_before !== $value->document_ref) {
                        $balance = $qty_open;
                    }

                    $kali = 1;
                    if ($value->job_type == 'EXP' || $value->job_type == 'TFRO' || $value->job_type == 'ADJ-') {
                        $kali = -1;
                    }

                    $qty = $kali * $value->qty;
                    $balance = $balance + $qty;

                    switch ($value->job_type) {
                        case 'IMP':
                            $job_desc = 'Inbound';
                            break;
                        case 'TFRI':
                            $job_desc = 'Transfer In';
                            break;
                        case 'ADJ+':
                            $job_desc = 'Adj. Plus';
                            break;
                        case 'EXP':
                            $job_desc = 'Outbound';
                            break;
                        case 'TFRO':
                            $job_desc = 'Transfer Out';
                            break;
                        case 'ADJ-':
                            $job_desc = 'Adj. Minus';
                            break;

                        default:
                            $job_desc = '';
                            break;
                    }

                    if ($principal->multi_level == "Yes") {
                        $list[] = [
                            "job_no" => $value->job_no,
                            "job_date" => $value->job_date,
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "job_type" => $job_desc,
                            "trx_qty1" => $value->pqty,
                            "trx_qty2" => $value->mqty,
                            "trx_qty3" => $value->bqty,
                            "bal_qty1" => ($balance  - ($balance % $value->uppp)) / $value->uppp,
                            "bal_qty2" => (($balance % $value->uppp) - (($balance % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bal_qty3" => $balance % $value->uppp % $value->muppp,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom,
                            "gross_weight" => $value->qty * $value->gross_weight,
                            "volume" => $value->qty * $value->volume,
                            "created_at" => $value->created_at,
                            "description" => $value->description,
                            "order_no" => $value->order_no,
                            "reference_no" => $value->reference_no,
                        ];
                    } else {
                        $list[] = [
                            "job_no" => $value->job_no,
                            "job_date" => $value->job_date,
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "job_type" => $job_desc,
                            "trx_qty1" => $value->pqty,
                            "bal_qty1" => ($balance  - ($balance % $value->uppp)) / $value->uppp,
                            "puom" => $value->puom,
                            "gross_weight" => $value->qty * $value->gross_weight,
                            "volume" => $value->qty * $value->volume,
                            "created_at" => $value->created_at,
                            "description" => $value->description,
                            "order_no" => $value->order_no,
                            "reference_no" => $value->reference_no,
                        ];
                    }

                    $product_before = $value->product_id;
                    $document_before = $value->document_ref;

                    if ($first_line) {
                        $first_line = false;
                    }
                }

                return new Collection($list);
                break;
            case 'product-site':
                $stockList = DB::table('iv_stock_transaction as a')
                    ->select(
                        'a.job_no',
                        'a.job_date',
                        'a.job_type',
                        'a.product_id',
                        'a.product_code',
                        'b.product_name',
                        'a.lot_no',
                        'a.document_ref',
                        'a.mfg_date',
                        'a.exp_date',
                        'a.site_id',
                        'c.site_name',
                        'd.area_name',
                        'a.location_code',
                        'b.uppp',
                        'b.muppp',
                        'a.qty',
                        'b.puom',
                        'b.muom',
                        'b.buom',
                        'a.pqty',
                        'a.mqty',
                        'a.bqty',
                        'a.qty',
                        'b.gross_weight',
                        'b.volume',
                        "a.created_at",
                        "f.order_no",
                        "a.reference_no",
                        DB::raw("CASE WHEN a.job_type = 'IMP' THEN e.description WHEN a.job_type = 'EXP' THEN g.description ELSE '' END as description")
                    )
                    ->join('iv_product as b', 'a.product_id', 'b.id')
                    ->leftjoin('iv_site as c', 'a.site_id', 'c.id')
                    ->leftjoin('iv_site_area as d', 'a.area_id', 'd.id')
                    ->leftjoin('iv_inbound_job as e', function ($data) {
                        $data->on("a.principal_id", "e.principal_id")
                            ->on("a.reference_no", "e.job_no");
                    })
                    ->leftjoin('iv_outbound_order as f', function ($data) {
                        $data->on("a.principal_id", "f.principal_id")
                            ->on("a.reference_no", "f.job_no");
                    })
                    ->leftjoin('iv_outbound_job as g', function ($data) {
                        $data->on("a.principal_id", "g.principal_id")
                            ->on("a.reference_no", "g.job_no");
                    })
                    ->where('a.company_id', $company_id)
                    ->where('a.principal_id', $this->principal)
                    ->where("a.branch_id", $this->branch_id)
                    ->whereBetween('b.product_code', [$this->product_from, $this->product_to])
                    ->whereIn(DB::raw("COALESCE(a.site_id, 0)"), $this->site_id)
                    ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $this->area_id)
                    ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$this->location_from, $this->location_to])
                    ->whereBetween('a.job_date', [date($this->date_from), date($this->date_to)])
                    ->whereIn('a.job_type', $job_type)
                    ->orderBy('b.product_name', 'asc')
                    ->orderBy('a.site_id', 'asc')
                    ->orderBy('a.area_id', 'asc')
                    ->orderBy('a.location_code', 'asc')
                    ->orderBy('a.job_date', 'asc')
                    ->orderBy(DB::raw("CASE WHEN a.job_type = 'IMP' THEN 1 WHEN a.job_type = 'TFRI' THEN 2 WHEN a.job_type = 'ADJ+' THEN 3 WHEN a.job_type = 'EXP' THEN 4 WHEN a.job_type = 'TFRO' THEN 5 ELSE 6 END"), 'asc')
                    ->distinct()->get();

                $first_line = true;
                $product_before = "";
                $document_before = "";
                $list = [];
                foreach ($stockList as $value) {
                    if ($product_before != $value->product_id || $document_before !== $value->site_id) {
                        $stock = $stockBefore->where('product_id', $value->product_id)
                            ->where('site_id', $value->site_id)
                            ->first();

                        $job_date = \Carbon\Carbon::parse($this->date_from)->format("d/m/Y");

                        if (isset($stock)) {
                            $qty_open = $stock->qty_received - $stock->qty_issue;

                            if ($principal->multi_level == "Yes") {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "trx_qty2" => 0,
                                    "trx_qty3" => 0,
                                    "bal_qty1" => ($qty_open  - ($qty_open % $value->uppp)) / $value->uppp,
                                    "bal_qty2" => (($qty_open % $value->uppp) - (($qty_open % $value->uppp) % $value->muppp)) / $value->muppp,
                                    "bal_qty3" => $qty_open % $value->uppp % $value->muppp,
                                    "puom" => $value->puom,
                                    "muom" => $value->muom,
                                    "buom" => $value->buom,
                                    "gross_weight" => $qty_open * $value->gross_weight,
                                    "volume" => $qty_open * $value->volume,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null
                                ];
                            } else {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "bal_qty1" => ($qty_open  - ($qty_open % $value->uppp)) / $value->uppp,
                                    "puom" => $value->puom,
                                    "gross_weight" => $qty_open * $value->gross_weight,
                                    "volume" => $qty_open * $value->volume,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null,
                                ];
                            }
                        } else {
                            if ($principal->multi_level == "Yes") {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "trx_qty2" => 0,
                                    "trx_qty3" => 0,
                                    "bal_qty1" => 0,
                                    "bal_qty2" => 0,
                                    "bal_qty3" => 0,
                                    "puom" => $value->puom,
                                    "muom" => $value->muom,
                                    "buom" => $value->buom,
                                    "gross_weight" => 0,
                                    "volume" => 0,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null,
                                ];
                            } else {
                                $list[] = [
                                    "job_no" => null,
                                    "job_date" => $job_date,
                                    "product_code" => null,
                                    "product_name" => null,
                                    "lot_no" => null,
                                    "site_name" => null,
                                    "area_name" => null,
                                    "location_code" => null,
                                    "job_type" => "Opening Balance",
                                    "trx_qty1" => 0,
                                    "bal_qty1" => 0,
                                    "puom" => $value->puom,
                                    "gross_weight" => 0,
                                    "volume" => 0,
                                    "created_at" => null,
                                    "description" => null,
                                    "order_no" => null,
                                    "reference_no" => null
                                ];
                            }

                            $qty_open = 0;
                        }
                    }

                    if ($product_before != $value->product_id || $document_before !== $value->site_id) {
                        $balance = $qty_open;
                    }

                    $kali = 1;
                    if ($value->job_type == 'EXP' || $value->job_type == 'TFRO' || $value->job_type == 'ADJ-') {
                        $kali = -1;
                    }

                    $qty = $kali * $value->qty;
                    $balance = $balance + $qty;

                    switch ($value->job_type) {
                        case 'IMP':
                            $job_desc = 'Inbound';
                            break;
                        case 'TFRI':
                            $job_desc = 'Transfer In';
                            break;
                        case 'ADJ+':
                            $job_desc = 'Adj. Plus';
                            break;
                        case 'EXP':
                            $job_desc = 'Outbound';
                            break;
                        case 'TFRO':
                            $job_desc = 'Transfer Out';
                            break;
                        case 'ADJ-':
                            $job_desc = 'Adj. Minus';
                            break;

                        default:
                            $job_desc = '';
                            break;
                    }

                    if ($principal->multi_level == "Yes") {
                        $list[] = [
                            "job_no" => $value->job_no,
                            "job_date" => $value->job_date,
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "job_type" => $job_desc,
                            "trx_qty1" => $value->pqty,
                            "trx_qty2" => $value->mqty,
                            "trx_qty3" => $value->bqty,
                            "bal_qty1" => ($balance  - ($balance % $value->uppp)) / $value->uppp,
                            "bal_qty2" => (($balance % $value->uppp) - (($balance % $value->uppp) % $value->muppp)) / $value->muppp,
                            "bal_qty3" => $balance % $value->uppp % $value->muppp,
                            "puom" => $value->puom,
                            "muom" => $value->muom,
                            "buom" => $value->buom,
                            "gross_weight" => $value->qty * $value->gross_weight,
                            "volume" => $value->qty * $value->volume,
                            "created_at" => $value->created_at,
                            "description" => $value->description,
                            "order_no" => $value->order_no,
                            "reference_no" => $value->reference_no,
                        ];
                    } else {
                        $list[] = [
                            "job_no" => $value->job_no,
                            "job_date" => $value->job_date,
                            "product_code" => $value->product_code,
                            "product_name" => $value->product_name,
                            "lot_no" => $value->lot_no,
                            "site_name" => $value->site_name,
                            "area_name" => $value->area_name,
                            "location_code" => $value->location_code,
                            "job_type" => $job_desc,
                            "trx_qty1" => $value->pqty,
                            "bal_qty1" => ($balance  - ($balance % $value->uppp)) / $value->uppp,
                            "puom" => $value->puom,
                            "gross_weight" => $value->qty * $value->gross_weight,
                            "volume" => $value->qty * $value->volume,
                            "created_at" => $value->created_at,
                            "description" => $value->description,
                            "order_no" => $value->order_no,
                            "reference_no" => $value->reference_no,
                        ];
                    }

                    $product_before = $value->product_id;
                    $document_before = $value->site_id;

                    if ($first_line) {
                        $first_line = false;
                    }
                }

                return new Collection($list);

                break;
        }
    }

    public function headings(): array
    {
        $principal = \App\Models\Master\Principal::find($this->principal);
        $header = [];

        switch ($this->group_on) {
            case 'product':
                if ($principal->multi_level == "Yes") {
                    return [
                        "Job No",
                        'Job Date',
                        "SKU No",
                        "SKU Name",
                        "Batch",
                        "Site Name",
                        "Area Name",
                        "Location",
                        "Job Type",
                        "1st Qty Trx",
                        "2nd Qty Trx",
                        "3rd Qty Trx",
                        "1st Qty Bal",
                        "2nd Qty Bal",
                        "3rd Qty Bal",
                        "1st Unit",
                        "2nd Unit",
                        "3rd Unit",
                        "Gross Weight",
                        "Volume",
                        "Confirmed Date",
                        "Description",
                        "Order No",
                        "Reference No",
                    ];
                } else {
                    $header = [
                        "Job No",
                        'Job Date',
                        "SKU No",
                        "SKU Name",
                        "Batch",
                        "Site Name",
                        "Area Name",
                        "Location",
                        "Job Type",
                        "Qty Trx",
                        "Qty Bal",
                        "Unit",
                        "Gross Weight",
                        "Volume",
                        "Confirmed Date",
                        "Description",
                        "Order No",
                        "Reference No",
                    ];
                }

                return $header;
                break;
            case 'product-lot':
                if ($principal->multi_level == "Yes") {
                    return [
                        "Job No",
                        'Job Date',
                        "SKU No",
                        "SKU Name",
                        "Batch",
                        "Site Name",
                        "Area Name",
                        "Location",
                        "Job Type",
                        "1st Qty Trx",
                        "2nd Qty Trx",
                        "3rd Qty Trx",
                        "1st Qty Bal",
                        "2nd Qty Bal",
                        "3rd Qty Bal",
                        "1st Unit",
                        "2nd Unit",
                        "3rd Unit",
                        "Gross Weight",
                        "Volume",
                        "Confirmed Date",
                        "Description",
                        "Order No",
                        "Reference No",
                    ];
                } else {
                    $header = [
                        "Job No",
                        'Job Date',
                        "SKU No",
                        "SKU Name",
                        "Batch",
                        "Site Name",
                        "Area Name",
                        "Location",
                        "Job Type",
                        "Qty Trx",
                        "Qty Bal",
                        "Unit",
                        "Gross Weight",
                        "Volume",
                        "Confirmed Date",
                        "Description",
                        "Order No",
                        "Reference No",
                    ];
                }

                return $header;
                break;
            case 'product-doc':
                if ($principal->multi_level == "Yes") {
                    return [
                        "Job No",
                        'Job Date',
                        "SKU No",
                        "SKU Name",
                        "Batch",
                        "Site Name",
                        "Area Name",
                        "Location",
                        "Job Type",
                        "1st Qty Trx",
                        "2nd Qty Trx",
                        "3rd Qty Trx",
                        "1st Qty Bal",
                        "2nd Qty Bal",
                        "3rd Qty Bal",
                        "1st Unit",
                        "2nd Unit",
                        "3rd Unit",
                        "Gross Weight",
                        "Volume",
                        "Confirmed Date",
                        "Description",
                        "Order No",
                        "Reference No",
                    ];
                } else {
                    $header = [
                        "Job No",
                        'Job Date',
                        "SKU No",
                        "SKU Name",
                        "Batch",
                        "Site Name",
                        "Area Name",
                        "Location",
                        "Job Type",
                        "Qty Trx",
                        "Qty Bal",
                        "Unit",
                        "Gross Weight",
                        "Volume",
                        "Confirmed Date",
                        "Description",
                        "Order No",
                        "Reference No",
                    ];
                }

                return $header;
                break;
            case 'product-site':
                if ($principal->multi_level == "Yes") {
                    return [
                        "Job No",
                        'Job Date',
                        "SKU No",
                        "SKU Name",
                        "Batch",
                        "Site Name",
                        "Area Name",
                        "Location",
                        "Job Type",
                        "1st Qty Trx",
                        "2nd Qty Trx",
                        "3rd Qty Trx",
                        "1st Qty Bal",
                        "2nd Qty Bal",
                        "3rd Qty Bal",
                        "1st Unit",
                        "2nd Unit",
                        "3rd Unit",
                        "Gross Weight",
                        "Volume",
                        "Confirmed Date",
                        "Description",
                        "Order No",
                        "Reference No",
                    ];
                } else {
                    $header = [
                        "Job No",
                        'Job Date',
                        "SKU No",
                        "SKU Name",
                        "Batch",
                        "Site Name",
                        "Area Name",
                        "Location",
                        "Job Type",
                        "Qty Trx",
                        "Qty Bal",
                        "Unit",
                        "Gross Weight",
                        "Volume",
                        "Confirmed Date",
                        "Description",
                        "Order No",
                        "Reference No",
                    ];
                }

                return $header;
                break;
        }
    }
}
