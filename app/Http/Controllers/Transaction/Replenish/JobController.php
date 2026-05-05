<?php

namespace App\Http\Controllers\Transaction\Replenish;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Master\Location as MasterLocation;
use App\Models\Master\Product as MasterProduct;
use App\Models\Transaction\Replenish\Batch as ReplenishBatch;
use App\Models\Transaction\Replenish\Job as ReplenishJob;

class JobController extends Controller
{
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

            $details = DB::table("iv_replenish_job as a")
                            ->select("a.*")
                            ->where("a.company_id", $company_id)
                            ->where("a.principal_id", $request->principal_id)
                            ->whereBetween('a.replenish_date', [$date_from, $date_to])
                            ->get();

            return datatables()->of($details)
            ->editColumn("replenish_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->replenish_date) );
            })
            ->editColumn("confirmed_flag", function ($data) {
                if ($data->confirmed_flag == "Yes") {
                    $status = "<div class='btn btn-sm btn-success'>Completed</div>";
                } else {
                    if ($data->allocated_flag == "Yes") {
                        $status = "<div class='btn btn-sm btn-warning'>Allocated</div>";
                    } else {
                        $status = "<div class='btn btn-sm btn-danger'>Open</div>";
                    }
                }
                return $status;
            })
            ->addColumn("replenish_no", function($data){
                $button = "";
                $button .= "<a href='" . URL("/inventory/stock-replenish/create/$data->id") . "' class='btn btn-default btn-sm'><i class='far fa-file'></i> " . $data->replenish_no . "</a>";
                return $button;
            })
            ->rawColumns(["confirmed_flag", "replenish_no"])
            ->addIndexColumn()
            ->make(true);
        }

        return view("transaction.replenish.index");
    }

    public function create($id) {
        $job_view = DB::table("iv_replenish_job as a")
                        ->select("a.*", "b.product_code as product_code_from", "b.product_name as product_name_from", "c.product_code as product_code_to", "c.product_name as product_name_to", "d.site_name", "e.area_name")
                        ->leftjoin("iv_product as b", "a.product_id_from", "b.id")
                        ->leftjoin("iv_product as c", "a.product_id_to", "c.id")
                        ->leftjoin("iv_site as d", "a.site_id", "d.id")
                        ->leftjoin("iv_site_area as e", "a.area_id", "e.id")
                        ->leftjoin("iv_location as f", "a.location_id_from", "f.id")
                        ->leftjoin("iv_location as g", "a.location_id_to", "g.id")
                        ->where("a.id", $id)
                        ->first();

        $data = [ "job_view" => $job_view ];

        return view("transaction.replenish.create", $data);
    }

    public function store(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $id = $request->replenish_id;
            $company_id = Auth::user()->company_id;
            $user_id = Auth::user()->username;
            $entry_date = \Carbon\Carbon::now();
            $replenish_date = \Carbon\Carbon::today();
            $created = \Carbon\Carbon::now();
            $entry_id = $request->entry_id;

            $year = $replenish_date->year;
            $month = $replenish_date->month;

            try {
                $job = ReplenishJob::find($id);

                if (is_null($id)) {
                    $job_number = ReplenishJob::where("company_id", $company_id)
                                    ->whereYear("replenish_date", $year)
                                    ->whereMonth("replenish_date", $month)->max("replenish_no");

                    if (is_null($job_number)) {
                        $increment = 1;
                    } else {
                        $increment = substr($job_number, 7, 4) + 1;
                    }

                    $replenish_no = "5" . $year . Str::of($month)->padLeft(2, "0") . Str::of($increment)->padLeft(4, "0");
                } else {
                    if ($job->allocated_flag == "Yes") {
                        DB::rollBack();
                        $message = ["error"=>"Already Allocated." ];

                        return $message;
                    } else {
                        $replenish_no = $job->replenish_no;
                    }
                }

                $allocated = "No";
                if (isset($entry_id)) {
                    $allocated = "Yes";
                }

                $data   =   ReplenishJob::updateOrCreate(["id" => $id],
                            [
                                "company_id"=>$company_id,
                                "replenish_no" => $replenish_no,
                                "replenish_date" => $replenish_date,
                                "principal_id" => $request->principal_id,
                                "product_id_from" => $request->product_id_from,
                                "product_id_to" => $request->product_id_to,
                                "site_id" => $request->site_id,
                                "area_id" => $request->area_id,
                                "location_id_from" => $request->location_id_from,
                                "location_code_from" => $request->location_code_from,
                                "location_id_to" => $request->location_id_to,
                                "location_code_to" => $request->location_code_to,
                                "allocated_flag" => $allocated,
                                "allocated_by" => $user_id,
                                "allocated_date" => $replenish_date,
                                "entry_date" => $entry_date
                            ]);

                foreach ($entry_id as $id) {
                    $location = MasterLocation::find($id);
                    $product = MasterProduct::find($location->product_id);

                    $actual_qty = $location->reorder_qty * $product->uppp;

                    $summary_qty = 0;
                    $i = 0;
                    while ($summary_qty < $actual_qty) {
                        $stock = StockLedger::from("iv_stock_ledger as a")
                                    ->select("a.*")
                                    ->join("iv_location as b", "a.location_id", "b.id")
                                    ->where("a.qtya", ">", 0)
                                    ->where("a.principal_id", $location->principal_id)
                                    ->where("a.product_id", $location->product_id)
                                    ->where(DB::raw("CASE WHEN a.qtys = a.qtya THEN 1 ELSE 0 END"), 1)
                                    ->where("a.freeze_flag", "No")
                                    ->where("b.status_code", "F")
                                    ->orderbyRaw("CASE WHEN b.status_code = 'P' THEN 2 ELSE 1 END asc")
                                    ->orderby("a.exp_date", "asc")
                                    ->orderby("a.qtya", "asc")
                                    ->first();

                        if (!isset($stock) || empty($stock)) {
                            DB::rollBack();
                            $message = ["error"=>["Quantity not available."]];

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

                        $stock->qtya = $stock->qtya - $qty;
                        $stock->qtyp = $stock->qtyp + $qty;
                        $stock->save();

                        $pqty = ($qty  - ($qty % $stock->uppp)) / $stock->uppp;
                        $mqty = (($qty % $stock->uppp) - (($qty % $stock->uppp) % $stock->muppp)) / $stock->muppp;
                        $bqty = $qty % $stock->uppp % $stock->muppp;

                        $picking = [];

                        $picking[] = [
                            "company_id" => $stock->company_id,
                            "principal_id" => $stock->principal_id,
                            "replenish_id" => $data->id,
                            "line_id" => $id,
                            "job_no" => $stock->job_no,
                            "job_type" => "TFRO",
                            "serial_id" => $stock->id,
                            "serial_no" => $stock->serial_no,
                            "product_id" => $stock->product_id,
                            "product_code" => $stock->product_code,
                            "po_number" => $stock->po_number,
                            "lot_no" => $stock->lot_no,
                            "document_ref" => $stock->document_ref,
                            "mfg_date" => $stock->mfg_date,
                            "exp_date" => $stock->exp_date,
                            "manufactur_id" => $stock->manufactur_id,
                            "status_id" => $stock->status_id,
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
                            "pallet_qty" =>$stock->pallet_qty,
                            "base_unit" => $stock->base_unit,
                            "reference_no" => $replenish_no,
                            "srno" => $stock->serial_no,
                            "created_at" => $created
                        ];

                        $serial = ReplenishBatch::where("company_id", $stock->company_id)
                                        ->where("principal_id", $stock->principal_id)
                                        ->where(DB::raw("left(serial_no, 1)"), "V")
                                        ->whereYear("created_at", $year)
                                        ->whereMonth("created_at", $month)->max("serial_no");

                        if (is_null($serial)) {
                            $last_number = 0;
                        } else {
                            $last_number = substr($serial, 7, 5);
                        }

                        $increment = $last_number + 1;

                        $serial_no =  "V" . $year . Str::of($month)->padLeft(2, "0") . Str::of($increment)->padLeft(5, "0");

                        $putaway = [];

                        $putaway[] = [
                            "company_id" => $stock->company_id,
                            "principal_id" => $stock->principal_id,
                            "replenish_id" => $data->id,
                            "line_id" => $id,
                            "job_no" => $stock->job_no,
                            "job_type" => "TFRI",
                            "serial_id" => 0,
                            "serial_no" => $serial_no,
                            "product_id" => $stock->product_id,
                            "product_code" => $stock->product_code,
                            "po_number" => $stock->po_number,
                            "lot_no" => $stock->lot_no,
                            "document_ref" => $stock->document_ref,
                            "mfg_date" => $stock->mfg_date,
                            "exp_date" => $stock->exp_date,
                            "manufactur_id" => $stock->manufactur_id,
                            "status_id" => $stock->status_id,
                            "site_id" => $location->site_id,
                            "area_id" => $location->area_id,
                            "location_id" => $location->id,
                            "location_code" => $location->location_code,
                            "puom" => $stock->puom,
                            "muom" => $stock->muom,
                            "buom" => $stock->buom,
                            "uppp" => $stock->uppp,
                            "muppp" => $stock->muppp,
                            "pqty" => $pqty,
                            "mqty" => $mqty,
                            "bqty" => $bqty,
                            "qty" => $qty,
                            "pallet_qty" =>$stock->pallet_qty,
                            "base_unit" => $stock->base_unit,
                            "reference_no" => $replenish_no,
                            "srno" => $stock->serial_no,
                            "created_at" => $created
                        ];

                        ReplenishBatch::insert($picking);
                        ReplenishBatch::insert($putaway);

                        $i++;
                    }
                }

                DB::commit();

                $message = ["success"=>url("/inventory/stock-replenish/create/" . $data->id)];

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
