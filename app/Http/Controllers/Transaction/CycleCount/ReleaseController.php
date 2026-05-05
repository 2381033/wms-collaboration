<?php

namespace App\Http\Controllers\Transaction\CycleCount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\CycleCount\Detail as CycleCountDetail;
use App\Models\Transaction\CycleCount\Job as CycleCountJob;

class ReleaseController extends Controller
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
                            ->where("a.cyclecount_id", $request->cycle_id)
                            ->where("a.confirmed_flag", "No")
                            ->where(DB::raw("CASE WHEN a.pqty = a.actual_pqty AND a.mqty = a.actual_mqty AND a.bqty = a.actual_bqty THEN 1 ELSE 0 END"), 1)
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
            ->editColumn("actual_exp_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->actual_exp_date) );
            })
            ->editColumn("actual_mfg_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->actual_mfg_date) );
            })
            ->addColumn("check", function ($data) {
                return "<input type='checkbox' required='required' name='release_id[]' class='release-check' id='" . $data->id . "' value='" . $data->id . "'>";
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

            try {
                $data = $request->release_id;

                foreach ($data as $id) {
                    $detail = CycleCountDetail::find($id);

                    $serial = StockLedger::find($detail->serial_id);

                    $serial->freeze_flag = "No";
                    $serial->save();

                    $detail->confirmed_flag = "Yes";
                    $detail->confirmed_by = $confirmed_by;
                    $detail->confirmed_date = $confirmed_date;
                    $detail->save();
                }

                $job = CycleCountJob::find($detail->cyclecount_id);
                $detail_count = CycleCountDetail::where("cyclecount_id", $detail->cyclecount_id)
                                    ->where("confirmed_flag", "No")
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
