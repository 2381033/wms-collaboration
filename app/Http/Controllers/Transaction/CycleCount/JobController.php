<?php

namespace App\Http\Controllers\Transaction\CycleCount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\CycleCount\Detail as CycleCountDetail;
use App\Models\Transaction\CycleCount\Job as CycleCountJob;

class JobController extends Controller
{
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        $details = [];
        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            if (!empty($request->principal_id)) {
                $details = DB::table("iv_cyclecount_job as a")
                                ->select("a.*", "c.principal_name")
                                ->join("users_principal as b", "a.principal_id", "b.principal_id")
                                ->join("iv_principal as c", "a.principal_id", "c.id")
                                ->where("b.user_id", $user_id)
                                ->where("a.company_id", $company_id)
                                ->where("a.principal_id", $request->principal_id)
                                ->whereBetween('a.cyclecount_date', [$date_from, $date_to])
                                ->get();
            } else {
                $details = DB::table("iv_cyclecount_job as a")
                            ->select("a.*", "c.principal_name")
                            ->join("users_principal as b", "a.principal_id", "b.principal_id")
                            ->join("iv_principal as c", "a.principal_id", "c.id")
                            ->where("b.user_id", $user_id)
                            ->where("a.company_id", $company_id)
                            ->whereBetween('a.cyclecount_date', [$date_from, $date_to])
                            ->get();
            }

            return datatables()->of($details)
            ->editColumn("cyclecount_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->cyclecount_date) );
            })
            ->editColumn("confirmed_flag", function ($data) {
                if ($data->confirmed_flag == "Yes") {
                    $status = "<div class='btn btn-sm btn-success'>Completed</div>";
                } else {
                    $status = "<div class='btn btn-sm btn-danger'>Open</div>";
                }
                return $status;
            })
            ->addColumn("cyclecount_no", function($data){
                $button = "";
                $button .= "<a href='" . URL("/inventory/cycle-count/create/$data->id") . "' class='btn btn-default btn-sm'><i class='far fa-file'></i> " . $data->cyclecount_no . "</a>";
                return $button;
            })
            ->rawColumns(["confirmed_flag", "cyclecount_no"])
            ->addIndexColumn()
            ->make(true);
        }

        return view("transaction.cyclecount.index");
    }

    public function create($id = "") {
        $user_id = Auth::user()->id;

        $job_view = DB::table("iv_cyclecount_job as a")
                        ->select(
                            "a.*", "c.group_name as group_name_from", "d.group_name as group_name_to",
                            "e.brand_name as brand_name_from", "f.brand_name as brand_name_to",
                            "g.product_name as product_name_from", "h.product_name as product_name_to", "i.site_name",
                            "j.area_name"
                        )
                        ->join("users_principal as b", "a.principal_id", "b.principal_id")
                        ->leftjoin("iv_product_group as c", "a.group_id_from", "c.id")
                        ->leftjoin("iv_product_group as d", "a.group_id_to", "d.id")
                        ->leftjoin("iv_product_brand as e", "a.brand_id_from", "e.id")
                        ->leftjoin("iv_product_brand as f", "a.brand_id_to", "f.id")
                        ->leftjoin("iv_product as g", "a.product_id_from", "g.id")
                        ->leftjoin("iv_product as h", "a.product_id_to", "h.id")
                        ->leftjoin("iv_site as i", "a.site_id", "i.id")
                        ->leftjoin("iv_site_area as j", "a.area_id", "j.id")
                        ->where("b.user_id", $user_id)
                        ->where("a.id", $id)->first();


        $data = [ "job_view" => $job_view ];

        return view("transaction.cyclecount.create", $data);
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
            $id = $request->cyclecount_id;
            $cyclecount_date = \Carbon\Carbon::today();
            $created = \Carbon\Carbon::now();
            $year = $cyclecount_date->year;
            $month = $cyclecount_date->month;

            $principal_id = $request->principal_id;

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

            $area_id = $request->area_id;
            if (empty($request->area_id)) {
                $area_id = "%";
            }

            if (!empty($request->location_code_from) && !empty($request->location_code_to)) {
                $location_from = $request->location_code_from;
                $location_to = $request->location_code_to;
            } else {
                if (!empty($request->location_code_from) && empty($request->location_code_to)) {
                    $location_from = $request->location_code_from;
                    $location_to = "zzzzzzzzzzzzzzz";
                } else if (empty($request->location_code_from) && !empty($request->location_code_to)) {
                    $location_from = "";
                    $location_to = $request->location_code_to;
                } else {
                    $location_from = "";
                    $location_to = "zzzzzzzzzzzzzzz";
                }
            }

            try {
                if (is_null($id) || empty($id)) {
                    $job = CycleCountJob::where("company_id", $company_id)
                                    ->whereYear("cyclecount_date", $year)
                                    ->whereMonth("cyclecount_date", $month)->max("cyclecount_no");

                    if (is_null($job)) {
                        $increment = 1;
                    } else {
                        $increment = substr($job, 7, 4) + 1;
                    }

                    $cyclecount_no = "7" . $year . Str::of($month)->padLeft(2, "0") . Str::of($increment)->padLeft(4, "0");
                } else {
                    $job = CycleCountJob::find($id);

                    $cyclecount_no = $job->cyclecount_no;
                }

                $data   =   CycleCountJob::updateOrCreate(["id" => $id],
                    [
                        "company_id" => $company_id,
                        "principal_id" => $request->principal_id,
                        "cyclecount_no" => $cyclecount_no,
                        "cyclecount_date" => $cyclecount_date,
                        "description" => $request->description,
                        "group_id_from" => $request->group_id_from,
                        "group_id_to" => $request->group_id_to,
                        "brand_id_from" => $request->brand_id_from,
                        "brand_id_to" => $request->brand_id_to,
                        "product_id_from" => $request->product_id_from,
                        "product_id_to" => $request->product_id_to,
                        "site_id" => $request->site_id,
                        "area_id" => $request->area_id,
                        "location_id_from" => $request->location_id_from,
                        "location_code_from" => $request->location_code_from,
                        "location_id_to" => $request->location_id_to,
                        "location_code_to" => $request->location_code_to,
                    ]);

                $stock = DB::table("iv_stock_ledger as a")
                        ->select(
                            "a.*", "c.product_name", "d.site_name", "e.area_name",
                            DB::raw("convert((a.qtys  - (a.qtys % c.uppp)) / c.uppp, int) as pqty"),
                            DB::raw("convert(((a.qtys % c.uppp) - ((a.qtys % c.uppp) % c.muppp)) / c.muppp, int) as mqty"),
                            DB::raw("a.qtys % c.uppp % c.muppp as bqty"), "c.puom", "c.muom", "c.buom"
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
                        ->where("a.area_id", "like", $area_id)
                        ->whereBetween("a.location_code", [ $location_from, $location_to ])
                        ->where("a.qtys", ">", 0)
                        ->get();

                $serial_list = [];

                foreach ($stock as $value) {
                    $detail = [];

                    $detail[] = [
                        "company_id" => $value->company_id,
                        "principal_id" => $value->principal_id,
                        "cyclecount_id" => $data->id,
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
                        "reference_no" => $cyclecount_no
                    ];

                    CycleCountDetail::insert($detail);

                    $serial_list[] = [
                        "id" => $value->id
                    ];
                }

                StockLedger::whereIn("id", $serial_list)->update(["freeze_flag"=>"Yes", "freeze_by"=>$user_name, "freeze_date"=>$created, "freeze_reason"=>"CY ".$cyclecount_no]);

                DB::commit();

                $message = ["success"=>url("/inventory/cycle-count/create/" . $data->id)];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ["error"=>$e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
