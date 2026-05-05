<?php

namespace App\Http\Controllers\Transaction\CycleCount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\CycleCount\Detail as CycleCountDetail;
use App\Models\Transaction\CycleCount\Job as CycleCountJob;
use App\Models\Transaction\CycleCount\Batch as CycleCountBatch;

class ConfirmController extends Controller
{
    public function index(Request $request) {
        $this->menu_name = "Cycle-Count";

        $details = [];
        if ($request->ajax()) {
            if (!empty($request->cycle_id) && !empty($request->cycle_id)) {
                $details = DB::table("iv_cyclecount_detail as a")
                            ->select("a.*", "b.product_name", "c.site_name", "d.area_name")
                            ->join("iv_product as b", "a.product_id", "b.id")
                            ->leftjoin("iv_site as c", "a.site_id", "c.id")
                            ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                            ->where("a.cyclecount_id", "=", $request->cycle_id)
                            ->where("a.confirmed_flag", "=", "No")
                            ->where(DB::raw("CASE WHEN a.pqty <> a.actual_pqty OR a.mqty <> a.actual_mqty OR a.bqty <> a.actual_bqty THEN 1 ELSE 0 END"), "=", 1)
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
                return "<input type='checkbox' required='required' name='invest_id[]' class='confirm-check' id='" . $data->id . "' value='" . $data->id . "'>";
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
            $invest_date = \Carbon\Carbon::today();

            try {
                $data = $request->invest_id;

                foreach ($data as $id) {
                    $detail = CycleCountDetail::find($id);
                    $job = CycleCountJob::find($detail->cyclecount_id);

                    if ($detail->serial_id !== 0) {
                        $qty = $detail->actual_qty - $detail->qty;

                        $serial = StockLedger::find($detail->serial_id);

                        $batch = [];
                        $batch[] = [
                            "company_id" => $serial->company_id,
                            "principal_id" => $serial->principal_id,
                            "cyclecount_id" => $detail->cyclecount_id,
                            "serial_id" => $detail->serial_id,
                            "serial_no" => $detail->serial_no,
                            "line_no" => $id,
                            "job_no" => $serial->job_no,
                            "job_date" => $invest_date,
                            "vehicle_no" => $serial->vehicle_no,
                            "product_id" => $serial->product_id,
                            "product_code" => $serial->product_code,
                            "po_number" => $serial->po_number,
                            "lot_no" => $serial->lot_no,
                            "document_ref" => $serial->document_ref,
                            "mfg_date" => $serial->mfg_date,
                            "exp_date" => $serial->exp_date,
                            "manufactur_id" => $serial->manufactur_id,
                            "status_id" => $serial->status_id,
                            "site_id" => $serial->site_id,
                            "area_id" => $serial->area_id,
                            "location_id" => $serial->location_id,
                            "location_code" => $serial->location_code,
                            "puom" => $serial->puom,
                            "muom" => $serial->muom,
                            "buom" => $serial->buom,
                            "uppp" => $serial->uppp,
                            "muppp" => $serial->muppp,
                            "qtyr" => $qty,
                            "qtys" => $qty,
                            "qtya" => $qty,
                            "qtyp" => 0,
                            "pallet_qty" => $serial->pallet_qty,
                            "base_unit" => $serial->base_unit,
                            "reference_no" => $job->cyclecount_no
                        ];

                        CycleCountBatch::insert($batch);

                        $serial->freeze_flag = "No";
                        $serial->save();
                    }

                    $detail->confirmed_flag = "Yes";
                    $detail->confirmed_by = $confirmed_by;
                    $detail->confirmed_date = $confirmed_date;
                    $detail->save();
                }

                $job = CycleCountJob::find($detail->cyclecount_id);
                $detail_count = CycleCountDetail::where("cyclecount_id", "=", $detail->cyclecount_id)
                                    ->where("confirmed_flag", "=", "No")
                                    ->get();

                if (is_null($detail_count)) {
                    $count = 0;
                } else {
                    $count = $detail_count->count();
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
