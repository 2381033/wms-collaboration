<?php

namespace App\Http\Controllers\Transaction\Transfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Transfer\Detail as TransferDetail;
use App\Models\Transaction\Transfer\Batch as TransferBatch;
use App\Models\Master\Location as MasterLocation;

class ProcessController extends Controller
{
    public function index(Request $request) {
        $details = [];
        if ($request->ajax()) {
            if (!empty($request->transfer_id) && !empty($request->transfer_id)) {
                $details = DB::table("iv_transfer_detail as a")
                            ->select("a.*", "b.product_name", "c.site_name", "d.area_name", "e.site_name as dest_site_name", "f.area_name as dest_area_name")
                            ->join("iv_product as b", "a.product_id", "b.id")
                            ->leftjoin("iv_site as c", "a.site_id", "c.id")
                            ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                            ->leftjoin("iv_site as e", "a.dest_site_id", "e.id")
                            ->leftjoin("iv_site_area as f", "a.dest_area_id", "f.id")
                            ->where("a.transfer_id", $request->transfer_id)
                            ->where("a.picked_flag", "No")
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

                $i = 0;
                foreach ($data as $id) {
                    $detail = TransferDetail::find($id);

                    $picking = [];

                    $picking[] = [
                        "company_id" => $detail->company_id,
                        "principal_id" => $detail->principal_id,
                        "transfer_id" => $detail->transfer_id,
                        "line_id" => $id,
                        "job_no" => $detail->job_no,
                        "job_type" => "TFRO",
                        "serial_id" => $detail->serial_id,
                        "serial_no" => $detail->serial_no,
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
                        "reference_no" => $detail->job_no,
                        "srno" => $detail->serial_no,
                        "created_at" => $created
                    ];

                    $serial = TransferBatch::where("company_id", $detail->company_id)
                                    ->where("principal_id", $detail->principal_id)
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
                        "company_id" => $detail->company_id,
                        "principal_id" => $detail->principal_id,
                        "transfer_id" => $detail->transfer_id,
                        "line_id" => $id,
                        "job_no" => $detail->job_no,
                        "job_type" => "TFRI",
                        "serial_id" => 0,
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
                        "site_id" => $detail->dest_site_id,
                        "area_id" => $detail->dest_area_id,
                        "location_id" => $detail->dest_location_id,
                        "location_code" => $detail->dest_location_code,
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
                        "reference_no" => $detail->job_no,
                        "srno" => $detail->serial_no,
                        "created_at" => $created
                    ];

                    TransferBatch::insert($picking);
                    TransferBatch::insert($putaway);

                    $stock = StockLedger::find($detail->serial_id);

                    if ( $detail->actual_qty > $stock->qtya ) {
                        DB::rollBack();

                        $message = ["error"=>"Stock available : " . $stock->qtya];

                        return $message;
                    }

                    $stock->qtya = $stock->qtya - $detail->actual_qty;
                    $stock->qtyp = $stock->qtyp + $detail->actual_qty;
                    $stock->save();

                    $stockDest = StockLedger::from("iv_stock_ledger as a")
                                    ->select("a.*", "b.status_code")
                                    ->leftJoin("iv_location as b", "a.location_id", "b.id")
                                    ->where("a.company_id", $detail->company_id)
                                    ->where("a.principal_id", $detail->principal_id)
                                    ->where("a.site_id", $detail->dest_site_id)
                                    ->where("a.area_id", $detail->dest_area_id)
                                    ->where("a.location_id", $detail->dest_location_id)
                                    ->where("a.qtys", ">", 0)
                                    ->first();

                    if (is_null($stockDest)) {
                        $count = 0;
                    } else {
                        $count = $stockDest->count();
                    }

                    if ($count == 0) {
                        $location = MasterLocation::find($detail->dest_location_id);

                        if ($location->status_code == "E") {
                            $location->status_code = "R";
                            $location->save();
                        }
                    } else {
                        $qty = $stockDest->qtys + $detail->actual_qty;

                        if ( $stockDest->status_code == "F" ) {
                            if ($stockDest->product_id != $detail->product_id) {
                                DB::rollBack();

                                $message = ["error"=>"Product name not same!!!"];

                                return $message;
                            }

                            if ($stockDest->lot_no != $detail->lot_no) {
                                DB::rollBack();

                                $message = ["error"=>"Batch Number not same!!!"];

                                return $message;
                            }
                            if ($qty > $detail->pallet_qty) {
                                DB::rollBack();

                                $message = ["error"=>"Pallet capacity overload!!!"];

                                return $message;
                            }
                        }
                    }

                    $detail->picked_flag = "Yes";
                    $detail->picked_by = $confirmed_by;
                    $detail->picked_date = $confirmed_date;
                    $detail->save();

                    $i++;
                }

                DB::commit();

                $message = ["success"=>"Sukses"];

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
