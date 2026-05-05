<?php

namespace App\Http\Controllers\Transaction\Replenish;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Replenish\Batch as ReplenishBatch;
use App\Models\Transaction\Replenish\Job as ReplenishJob;

class CancelController extends Controller
{
    public function index(Request $request) {
        $details = [];
        if ($request->ajax()) {
            if (!empty($request->replenish_id) && !empty($request->replenish_id)) {
                $details = DB::table("iv_replenish_batch as a")
                                ->select("a.*", "b.product_name", "c.site_name", "d.area_name")
                                ->join("iv_product as b", "a.product_id", "b.id")
                                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                                ->where("a.replenish_id", $request->replenish_id)
                                ->where("a.confirmed_flag", "No")
                                ->where("a.job_type", "TFRO")
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
                return "<input type='checkbox' required='required' name='cancel_id[]' class='cancel-check' id='" . $data->id . "' value='" . $data->id . "'>";
            })
            ->rawColumns(["check"])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function submit(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $data = $request->cancel_id;

                foreach ($data as $id) {
                    $detail = ReplenishBatch::find($id);
                    $job = ReplenishJob::find($detail->replenish_id);
                    $serial = StockLedger::find($detail->serial_id);

                    $serial->qtya = $serial->qtya + $detail->qty;
                    $serial->qtyp = $serial->qtyp - $detail->qty;
                    $serial->save();

                    ReplenishBatch::where("srno", $detail->serial_no)->delete();
                }

                $batch = ReplenishBatch::where("replenish_id", $job->id)->get();

                if (is_null($batch)) {
                    $count = 0;
                } else {
                    $count = $batch->count();
                }

                if ($count == 0) {
                    $job->allocated_flag = "No";
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
