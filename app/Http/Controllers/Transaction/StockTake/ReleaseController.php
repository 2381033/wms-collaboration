<?php

namespace App\Http\Controllers\Transaction\StockTake;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\StockTake\Detail as StockTakeDetail;
use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\StockTake\Job as StockTakeJob;

class ReleaseController extends Controller
{
    public function index(Request $request) {
        $details = [];
        if ($request->ajax()) {
            if (!empty($request->take_id) && !empty($request->take_id)) {
                $details = DB::table("iv_stocktake_detail as a")
                            ->select("a.*", "b.product_name", "c.site_name", "d.area_name")
                            ->join("iv_product as b", "a.product_id", "b.id")
                            ->leftjoin("iv_site as c", "a.site_id", "c.id")
                            ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                            ->where("a.stocktake_id", $request->take_id)
                            ->where("a.confirmed_flag", "No")
                            ->where(
                                DB::raw("CASE WHEN a.pqty = a.actual_pqty AND a.mqty = a.actual_mqty AND a.bqty = a.actual_bqty AND COALESCE(a.lot_no, '') = COALESCE(a.actual_lot_no, '') AND COALESCE(a.mfg_date, '') = COALESCE(a.actual_mfg_date, '') AND COALESCE(a.exp_date, '') = COALESCE(a.actual_exp_date, '') THEN 1 ELSE 0 END"), 1
                            )
                            ->get();
            }

            return datatables()->of($details)
            ->editColumn('mfg_date', function ($data)
            {
                $mfg_date = "";
                if (isset($data->mfg_date)) {
                    $mfg_date = date('d/m/Y', strtotime($data->mfg_date) );
                }
                return $mfg_date;
            })
            ->editColumn('exp_date', function ($data)
            {
                $exp_date = "";
                if (isset($data->exp_date)) {
                    $exp_date = date('d/m/Y', strtotime($data->exp_date) );
                }
                return $exp_date;
            })
            ->editColumn('actual_mfg_date', function ($data)
            {
                $actual_mfg_date = "";
                if (isset($data->actual_mfg_date)) {
                    $actual_mfg_date = date('d/m/Y', strtotime($data->actual_mfg_date) );
                }
                return $actual_mfg_date;
            })
            ->editColumn('actual_exp_date', function ($data)
            {
                $actual_exp_date = "";
                if (isset($data->actual_exp_date)) {
                    $actual_exp_date = date('d/m/Y', strtotime($data->actual_exp_date) );
                }
                return $actual_exp_date;
            })
            ->addColumn("check", function ($data) {
                return "<input type='checkbox' required='required' name='release_id[]' class='release-check' data-id='" . $data->id . "' value='" . $data->id . "'>";
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
                    $detail = StockTakeDetail::find($id);

                    $serial = StockLedger::find($detail->serial_id);

                    if ( isset($serial) ) {
                        $serial->freeze_flag = "No";
                        $serial->save();

                        $detail->confirmed_flag = "Yes";
                        $detail->confirmed_by = $confirmed_by;
                        $detail->confirmed_date = $confirmed_date;
                        $detail->save();
                    }
                }

                $job = StockTakeJob::find($request->take_release);
                $detail_count = StockTakeDetail::where("stocktake_id", $request->take_release)
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
