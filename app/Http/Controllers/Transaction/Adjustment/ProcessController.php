<?php

namespace App\Http\Controllers\Transaction\Adjustment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Adjustment\Job as AdjustmentJob;
use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Master\Location as MasterLocation;
use App\Models\Transaction\Adjustment\Batch as AdjustmentBatch;
use App\Models\Transaction\Adjustment\Detail as AdjustmentDetail;

class ProcessController extends Controller
{
    public function index(Request $request) {
        $details = [];
        if ($request->ajax()) {
            if (!empty($request->adjust_id) && !empty($request->adjust_id)) {
                $details = DB::table("iv_adjustment_detail as a")
                            ->select("a.*", "b.principal_name", "c.product_name", "d.site_name", "e.area_name")
                            ->join("iv_principal as b", "a.principal_id", "b.id")
                            ->join("iv_product as c", "a.product_id", "c.id")
                            ->leftjoin("iv_site as d", "a.site_id", "d.id")
                            ->leftjoin("iv_site_area as e", "a.area_id", "e.id")
                            ->where("a.adjust_id", "=", $request->adjust_id)
                            ->where("a.picked_flag", "=", "No")
                            ->get();
            }

            return datatables()->of($details)
            ->editColumn("exp_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->exp_date) );
            })
            ->editColumn("mfg_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->mfg_date) );
            })
            ->addColumn("check", function ($data) {
                return "<input type='checkbox' required='required' name='process_id[]' class='process-check' id='" . $data->id . "' value='" . $data->id . "'>";
            })
            ->rawColumns(["check"])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function submit(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            $confirmed_by = Auth::user()->username;
            $confirmed_date = \Carbon\Carbon::now();
            $created = \Carbon\Carbon::now();
            $date = \Carbon\Carbon::today();

            $year = $date->year;
            $month = $date->month;

            try {
                $data = $request->process_id;

                foreach ($data as $id) {
                    $detail = AdjustmentDetail::find($id);
                    $job = AdjustmentJob::find($detail->adjust_id);

                    if ($detail->status_flag == "Exist") {
                        if ($detail->adjust_type == "Minus") {
                            $stock = StockLedger::find($detail->serial_id);

                            $stock->qtya = $stock->qtya - $detail->actual_qty;
                            $stock->qtyp = $stock->qtyp + $detail->actual_qty;
                            $stock->save();

                            $job_type = "ADJ-";
                        } else {
                            $job_type = "ADJ+";
                        }

                        $serial_no = $detail->serial_no;
                    } else {
                        if ($detail->adjust_type == "Plus") {
                            $location = MasterLocation::find($detail->location_id);

                            if ($location->status_code == "E") {
                                $location->status_code = "R";
                                $location->save();
                            }

                            $serial = AdjustmentBatch::where("company_id", "=", $detail->company_id)
                                        ->where("principal_id", "=", $detail->principal_id)
                                        ->where(DB::raw("left(serial_no, 1)"), "=", "A")
                                        ->whereYear("created_at", "=", $year)
                                        ->whereMonth("created_at", "=", $month)->max("serial_no");

                            if (is_null($serial)) {
                                $last_number = 0;
                            } else {
                                $last_number = substr($serial, 7, 5);
                            }

                            $increment = $last_number + 1;

                            $serial_no =  "A" . $year . Str::of($month)->padLeft(2, "0") . Str::of($increment)->padLeft(5, "0");
                            $job_type = "ADJ+";
                        }
                    }

                    $adjustment = [];

                    $adjustment[] = [
                        "company_id" => $detail->company_id,
                        "principal_id" => $detail->principal_id,
                        "adjust_id" => $detail->adjust_id,
                        "line_id" => $id,
                        "job_no" => $detail->job_no,
                        "job_type" => $job_type,
                        "serial_id" => $detail->serial_id,
                        "serial_no" => $serial_no,
                        "product_id" => $detail->product_id,
                        "product_code" => $detail->product_code,
                        "po_number" => $detail->po_number,
                        "lot_no" => $detail->lot_no,
                        "document_ref" => $detail->document_ref,
                        "mfg_date" => $detail->mfg_date,
                        "exp_date" => $detail->exp_date,
                        "manufactur_id" => $detail->manufactur_id,
                        "status_id" => $detail->status_id,
                        "site_id" => $detail->site_id,
                        "area_id" => $detail->area_id,
                        "location_id" => $detail->location_id,
                        "location_code" => $detail->location_code,
                        "puom" => $detail->puom,
                        "muom" => $detail->muom,
                        "buom" => $detail->buom,
                        "uppp" => $detail->uppp,
                        "muppp" => $detail->muppp,
                        "pqty" => $detail->actual_pqty,
                        "mqty" => $detail->actual_mqty,
                        "bqty" => $detail->actual_bqty,
                        "qty" => $detail->actual_qty,
                        "pallet_qty" =>$detail->pallet_qty,
                        "base_unit" => $detail->base_unit,
                        "reference_no" => $job->adjust_no,
                        "created_at" => $created
                    ];

                    AdjustmentBatch::insert($adjustment);

                    $detail->picked_flag = "Yes";
                    $detail->picked_by = $confirmed_by;
                    $detail->picked_date = $confirmed_date;
                    $detail->save();
                }

                DB::commit();

                $message = ["success"=>"Successfully"];

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
