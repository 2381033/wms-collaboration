<?php

namespace App\Http\Controllers\Transaction\Replenish;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Stock\Transaction as StockTransaction;
use App\Models\Transaction\Replenish\Batch as ReplenishBatch;
use App\Models\Transaction\Replenish\Job as ReplenishJob;

class ConfirmController extends Controller
{
    public function index(Request $request) {
        $this->menu_name = "Replenishment";

        $details = [];
        if ($request->ajax()) {
            if (!empty($request->replenish_id) && !empty($request->replenish_id)) {
                $details = ReplenishBatch::from("iv_replenish_batch as a")
                                ->select("a.*", "b.product_name", "c.site_name", "d.area_name")
                                ->join("iv_product as b", "a.product_id", "b.id")
                                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                                ->where("a.replenish_id", "=", $request->replenish_id)
                                ->where("a.confirmed_flag", "=", "No")
                                ->where("a.job_type", "=", "TFRO")
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
                return "<input type='checkbox' required='required' name='confirm_id[]' class='confirm-check' id='" . $data->id . "' value='" . $data->id . "'>";
            })
            ->rawColumns(["check"])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function submit(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            $job_date = \Carbon\Carbon::today();
            $confirmed_by = Auth::user()->username;
            $confirmed_date = \Carbon\Carbon::now();
            try {
                $data = $request->confirm_id;

                foreach ($data as $id) {
                    $detail = ReplenishBatch::find($id);
                    $job = ReplenishJob::find($detail->replenish_id);
                    $serial = StockLedger::find($detail->serial_id);

                    $serial->qtys = $serial->qtys - $detail->qty;
                    $serial->qtyp = $serial->qtyp - $detail->qty;
                    $serial->save();

                    $batch = ReplenishBatch::where("replenish_id", $detail->replenish_id)
                            ->where("srno", $detail->serial_no)
                            ->where("job_type", "TFRI")
                            ->first();

                    $stock_ledger = [];
                    $stock_ledger[] = [
                        "company_id" => $batch->company_id,
                        "principal_id" => $batch->principal_id,
                        "serial_no" => $batch->serial_no,
                        "srno" => $batch->srno,
                        "line_no" => $id,
                        "job_no" => $batch->job_no,
                        "job_date" => $job_date,
                        "vehicle_no" => $batch->vehicle_no,
                        "product_id" => $batch->product_id,
                        "product_code" => $batch->product_code,
                        "po_number" => $batch->po_number,
                        "lot_no" => $batch->lot_no,
                        "document_ref" => $batch->document_ref,
                        "mfg_date" => $batch->mfg_date,
                        "exp_date" => $batch->exp_date,
                        "manufactur_id" => $batch->manufactur_id,
                        "status_id" => $batch->status_id,
                        "site_id" => $batch->site_id,
                        "area_id" => $batch->area_id,
                        "location_id" => $batch->location_id,
                        "location_code" => $batch->location_code,
                        "puom" => $batch->puom,
                        "muom" => $batch->muom,
                        "buom" => $batch->buom,
                        "uppp" => $batch->uppp,
                        "muppp" => $batch->muppp,
                        "pqty" => $batch->pqty,
                        "mqty" => $batch->mqty,
                        "bqty" => $batch->bqty,
                        "qtyr" => $batch->qty,
                        "qtys" => $batch->qty,
                        "qtya" => $batch->qty,
                        "qtyp" => 0,
                        "pallet_qty" => $batch->pallet_qty,
                        "base_unit" => $batch->base_unit,
                        "reference_no" => $batch->reference_no
                    ];

                    $tranfer_out = [];

                    $tranfer_out[] = [
                        "company_id" => $detail->company_id,
                        "principal_id" => $detail->principal_id,
                        "serial_no" => $detail->serial_no,
                        "srno" => $batch->srno,
                        "line_no" => $detail->id,
                        "job_no" => $detail->job_no,
                        "job_date" => $job_date,
                        "job_type" => "TFRO",
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
                        "pqty" => $detail->pqty,
                        "mqty" => $detail->mqty,
                        "bqty" => $detail->bqty,
                        "qty" => $detail->qty,
                        "base_unit" => $detail->base_unit,
                        "reference_no" => $detail->reference_no
                    ];

                    $tranfer_in = [];

                    $tranfer_in[] = [
                        "company_id" => $batch->company_id,
                        "principal_id" => $batch->principal_id,
                        "serial_no" => $batch->serial_no,
                        "srno" => $batch->srno,
                        "line_no" => $batch->id,
                        "job_no" => $batch->job_no,
                        "job_date" => $job_date,
                        "job_type" => "TFRI",
                        "product_id" => $batch->product_id,
                        "product_code" => $batch->product_code,
                        "po_number" => $batch->po_number,
                        "lot_no" => $batch->lot_no,
                        "document_ref" => $batch->document_ref,
                        "mfg_date" => $batch->mfg_date,
                        "exp_date" => $batch->exp_date,
                        "manufactur_id" => $batch->manufactur_id,
                        "status_id" => $batch->status_id,
                        "site_id" => $batch->site_id,
                        "area_id" => $batch->area_id,
                        "location_id" => $batch->location_id,
                        "location_code" => $batch->location_code,
                        "puom" => $batch->puom,
                        "muom" => $batch->muom,
                        "buom" => $batch->buom,
                        "uppp" => $batch->uppp,
                        "muppp" => $batch->muppp,
                        "pqty" => $batch->pqty,
                        "mqty" => $batch->mqty,
                        "bqty" => $batch->bqty,
                        "qty" => $batch->qty,
                        "base_unit" => $batch->base_unit,
                        "reference_no" => $batch->reference_no
                    ];

                    StockLedger::insert($stock_ledger);
                    StockTransaction::insert($tranfer_in);
                    StockTransaction::insert($tranfer_out);

                    $detail->confirmed_flag = "Yes";
                    $detail->confirmed_by = $confirmed_by;
                    $detail->confirmed_date = $confirmed_date;
                    $detail->save();

                    $batch->confirmed_flag = "Yes";
                    $batch->confirmed_by = $confirmed_by;
                    $batch->confirmed_date = $confirmed_date;
                    $batch->save();
                }

                $batch = ReplenishBatch::where("replenish_id", $job->id)
                            ->where("confirmed_flag", "No")
                            ->get();

                if (is_null($batch)) {
                    $count = 0;
                } else {
                    $count = $batch->count();
                }

                if ($count == 0) {
                    $job->confirmed_flag = "Yes";
                    $job->confirmed_by = $confirmed_by;
                    $job->confirmed_date = $confirmed_date;
                    $job->save();
                }

                DB::commit();

                $message = ["success"=>"Sukses"];

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
