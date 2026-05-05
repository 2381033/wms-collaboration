<?php

namespace App\Http\Controllers\Transaction\StockTake;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\StockTake\Detail as StockTakeDetail;
use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\StockTake\Job as StockTakeJob;
use App\Models\Master\SiteArea as MasterSiteArea;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $details = [];
        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            if (!empty($request->principal_id)) {
                $details = DB::table("iv_stocktake_job as a")
                            ->select("a.*", "c.principal_name")
                            ->join("users_principal as b", "a.principal_id", "b.principal_id")
                            ->join("iv_principal as c", "a.principal_id", "c.id")
                            ->where("b.user_id", $user_id)
                            ->where("a.company_id", $company_id)
                            ->where("a.principal_id", $request->principal_id)
                            ->whereBetween('a.stocktake_date', [$date_from, $date_to])
                            ->get();
            }

            return datatables()->of($details)
            ->editColumn("stocktake_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->stocktake_date) );
            })
            ->editColumn("confirmed_flag", function ($data) {
                if ($data->confirmed_flag == "Yes") {
                    $status = "<div class='btn btn-sm btn-success'>Completed</div>";
                } else {
                    $status = "<div class='btn btn-sm btn-danger'>Open</div>";
                }
                return $status;
            })
            ->addColumn("stocktake_no", function($data){
                $button = "";
                $button .= "<a href='" . URL("/inventory/stock-take/create/$data->id") . "' class='btn btn-default btn-sm'><i class='far fa-file'></i> " . $data->stocktake_no . "</a>";
                return $button;
            })
            ->rawColumns(["confirmed_flag", "stocktake_no"])
            ->addIndexColumn()
            ->make(true);
        }

        return view("transaction.stocktake.index");
    }

    public function create($id = "") {
        $user_id = Auth::user()->id;

        $job_view = DB::table("iv_stocktake_job as a")
                        ->select("a.*", "c.group_name as group_name_from", "d.group_name as group_name_to",
                            "e.brand_name as brand_name_from", "f.brand_name as brand_name_to",
                            "g.product_name as product_name_from", "h.product_name as product_name_to", "i.site_name",
                            "j.area_name as area_name_from", "k.area_name as area_name_to"
                        )
                        ->join("users_principal as b", "a.principal_id", "b.principal_id")
                        ->leftjoin("iv_product_group as c", "a.group_id_from", "c.id")
                        ->leftjoin("iv_product_group as d", "a.group_id_to", "d.id")
                        ->leftjoin("iv_product_brand as e", "a.brand_id_from", "e.id")
                        ->leftjoin("iv_product_brand as f", "a.brand_id_to", "f.id")
                        ->leftjoin("iv_product as g", "a.product_id_from", "g.id")
                        ->leftjoin("iv_product as h", "a.product_id_to", "h.id")
                        ->leftjoin("iv_site as i", "a.site_id", "i.id")
                        ->leftjoin("iv_site_area as j", "a.area_id_from", "j.id")
                        ->leftjoin("iv_site_area as k", "a.area_id_to", "k.id")
                        ->where("b.user_id", $user_id)
                        ->where("a.id", $id)->first();


        $data = [ "job_view" => $job_view ];

        return view("transaction.stocktake.create", $data);
    }

    public function store(Request $request) {
        $messsages = array(
            "principal_id.required"=>"Principal name field is required.",
            "description.required"=>"Description field is required.",
        );

        $rules = array(
            "principal_id" => "required",
            "description" => "required",
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(["error"=>$validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            $company_id = Auth::user()->company_id;
            $user_id = Auth::user()->id;
            $user_name = Auth::user()->username;
            $id = $request->stocktake_id;
            $stocktake_date = \Carbon\Carbon::today();
            $created = \Carbon\Carbon::now();
            $year = $stocktake_date->year;
            $month = $stocktake_date->month;

            $principal_id = $request->principal_id;
            $areaMax = MasterSiteArea::max('id');

            if (!empty($request->group_code_from) && !empty($request->group_code_to)) {
                $group_from = $request->group_code_from;
                $group_to = $request->group_code_to;
            } else {
                if (!empty($request->group_code_from) && empty($request->group_code_to)) {
                    $group_from = $request->group_code_from;
                    $group_to = "zzzzzzzzzz";
                } else if (empty($request->group_code_from) && !empty($request->group_code_to)) {
                    $group_from = "";
                    $group_to = $request->group_code_to;
                } else {
                    $group_from = "";
                    $group_to = "zzzzzzzzzz";
                }
            }

            if (!empty($request->brand_code_from) && !empty($request->brand_code_to)) {
                $brand_from = $request->brand_code_from;
                $brand_to = $request->brand_code_to;
            } else {
                if (!empty($request->brand_code_from) && empty($request->brand_code_to)) {
                    $brand_from = $request->brand_code_from;
                    $brand_to = "zzzzzzzzzz";
                } else if (empty($request->brand_code_from) && !empty($request->brand_code_to)) {
                    $brand_from = "";
                    $brand_to = $request->brand_code_to;
                } else {
                    $brand_from = "";
                    $brand_to = "zzzzzzzzzz";
                }
            }

            if (!empty($request->product_code_from) && !empty($request->product_code_to)) {
                $product_from = $request->product_code_from;
                $product_to = $request->product_code_to;
            } else {
                if (!empty($request->product_code_from) && empty($request->product_code_to)) {
                    $product_from = $request->product_code_from;
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                } else if (empty($request->product_code_from) && !empty($request->product_code_to)) {
                    $product_from = "";
                    $product_to = $request->product_code_to;
                } else {
                    $product_from = "";
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                }
            }

            $site_id = $request->site_id;
            if (empty($request->site_id)) {
                $site_id = "%";
            }

            if (!empty($request->area_id_from) && !empty($request->area_id_to)) {
                $area_from = $request->area_id_from;
                $area_to = $request->area_id_to;
            } else {
                if (!empty($request->area_id_from) && empty($request->area_id_to)) {
                    $area_from = $request->area_id_from;
                    $area_to = $areaMax;
                } else if (empty($request->area_id_from) && !empty($request->area_id_to)) {
                    $area_from = 1;
                    $area_to = $request->area_id_to;
                } else {
                    $area_from = 1;
                    $area_to = $areaMax;
                }
            }

            try {
                if (is_null($id) || empty($id)) {
                    $job = StockTakeJob::where("company_id", $company_id)
                                    ->whereYear("stocktake_date", $year)
                                    ->whereMonth("stocktake_date", $month)->max("stocktake_no");

                    if (is_null($job)) {
                        $increment = 1;
                    } else {
                        $increment = substr($job, 7, 4) + 1;
                    }

                    $stocktake_no = "6" . $year . Str::of($month)->padLeft(2, "0") . Str::of($increment)->padLeft(4, "0");
                } else {
                    $job = StockTakeJob::find($id);

                    $stocktake_no = $job->stocktake_no;
                }

                $stock = DB::table("iv_stock_ledger as a")
                            ->select(
                                DB::raw("convert((a.qtys  - (a.qtys % c.uppp)) / c.uppp, int) as pqty"),
                                DB::raw("convert(((a.qtys % c.uppp) - ((a.qtys % c.uppp) % c.muppp)) / c.muppp, int) as mqty"),
                                DB::raw("a.qtys % c.uppp % c.muppp as bqty"), "c.puom", "c.muom", "c.buom", "a.qtys", "c.uppp", "c.muppp",
                                "a.company_id", "a.principal_id", "a.id", "a.serial_no", "a.srno", "a.job_no", "a.job_date", "a.vehicle_no", "a.line_no",
                                "a.product_id", "a.product_code", "a.po_number", "a.lot_no", "a.document_ref", "a.mfg_date",
                                "a.exp_date", "a.manufactur_id", "a.status_id", "a.site_id", "a.area_id", "a.location_id",
                                "a.location_code", "a.pallet_qty", "a.base_unit", "a.reference_no",
                                "c.product_name", "d.site_name", "e.area_name"
                            )
                            ->join("users_site as b", "a.site_id", "b.site_id")
                            ->join("iv_product as c", "a.product_id", "c.id")
                            ->leftjoin("iv_site as d", "a.site_id", "d.id")
                            ->leftjoin("iv_site_area as e", "a.area_id", "e.id")
                            ->join("iv_product_group as f", "c.group_id", "f.id")
                            ->join("iv_product_brand as g", "c.brand_id", "g.id")
                            ->where("b.user_id", $user_id)
                            ->where("a.company_id", $company_id)
                            ->where("a.principal_id", $principal_id)
                            ->whereBetween("f.group_code", [ $group_from, $group_to ])
                            ->whereBetween("g.brand_code", [ $brand_from, $brand_to ])
                            ->whereBetween("c.product_code", [ $product_from, $product_to ])
                            ->where("a.site_id", "like", $site_id)
                            ->whereBetween("a.area_id", [ $area_from, $area_to ])
                            ->where("a.qtys", ">", 0)
                            ->get();

                if (!isset($stock) || $stock->count() == 0) {
                    return ["error"=>["Stock not available."]];
                }

                $data   =   StockTakeJob::updateOrCreate(["id" => $id],
                    [
                        "company_id" => $company_id,
                        "principal_id" => $request->principal_id,
                        "stocktake_no" => $stocktake_no,
                        "stocktake_date" => $stocktake_date,
                        "description" => $request->description,
                        "group_id_from" => $request->group_id_from,
                        "group_id_to" => $request->group_id_to,
                        "brand_id_from" => $request->brand_id_from,
                        "brand_id_to" => $request->brand_id_to,
                        "product_id_from" => $request->product_id_from,
                        "product_id_to" => $request->product_id_to,
                        "site_id" => $request->site_id,
                        "area_id_from" => $request->area_id_from,
                        "area_id_to" => $request->area_id_to,
                    ]);

                $serial_list = [];
                foreach ($stock as $value) {
                    $detail = [];

                    $detail[] = [
                        "company_id" => $value->company_id,
                        "principal_id" => $value->principal_id,
                        "stocktake_id" => $data->id,
                        "job_no" => $value->job_no,
                        "serial_id" => $value->id,
                        "serial_no" => $value->serial_no,
                        "product_id" => $value->product_id,
                        "product_code" => $value->product_code,
                        "po_number" => $value->po_number,
                        "lot_no" => $value->lot_no,
                        "document_ref" => $value->document_ref,
                        "mfg_date" => $value->mfg_date,
                        "exp_date" => $value->exp_date,
                        "manufactur_id" => $value->manufactur_id,
                        "status_id" => $value->status_id,
                        "site_id" => $value->site_id,
                        "area_id" => $value->area_id,
                        "location_id" => $value->location_id,
                        "location_code" => $value->location_code,
                        "puom" => $value->puom,
                        "muom" => $value->muom,
                        "buom" => $value->buom,
                        "uppp" => $value->uppp,
                        "muppp" => $value->muppp,
                        "pqty" => $value->pqty,
                        "mqty" => $value->mqty,
                        "bqty" => $value->bqty,
                        "qty" => $value->qtys,
                        "actual_pqty" => 0,
                        "actual_mqty" => 0,
                        "actual_bqty" => 0,
                        "actual_qty" => 0,
                        "pallet_qty" => $value->pallet_qty,
                        "base_unit" => $value->base_unit,
                        "reference_no" => $stocktake_no
                    ];

                    StockTakeDetail::insert($detail);

                    $serial_list[] = [
                        "id" => $value->id
                    ];
                }

                StockLedger::whereIn("id", $serial_list)->update(["freeze_flag"=>"Yes", "freeze_by"=>$user_name, "freeze_date"=>$created, "freeze_reason"=>"CY ".$stocktake_no]);

                DB::commit();

                $message = ["success"=>url("/inventory/stock-take/create/" . $data->id)];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ["error"=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
