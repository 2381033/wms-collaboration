<?php

namespace App\Http\Controllers\Transaction\Outbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Outbound\Batch as outboundBatch;
use App\Models\Transaction\Outbound\Detail as outboundDetails;
use App\Models\Transaction\Stock\Ledger as stockLedger;

class CancelController extends Controller
{
    public function index(Request $request)
    {
        $company_id = Auth::user()->company_id;

        if ($request->ajax()) {
            $list_data = DB::table("iv_outbound_batch as a")
                ->select("a.*", "b.product_name", "c.site_name", "d.area_name")
                ->join("iv_product as b", "a.product_id", "b.id")
                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                ->where("a.company_id", $company_id)
                ->where("a.outbound_id", $request->outbound_id)
                ->where("a.confirmed_flag", "No")
                ->get();

            return datatables()->of($list_data)
                ->editColumn('exp_date', function ($data) {
                    $exp_date = "";
                    if (isset($data->exp_date)) {
                        $exp_date = date('d/m/Y', strtotime($data->exp_date));
                    }
                    return $exp_date;
                })
                ->editColumn('mfg_date', function ($data) {
                    $mfg_date = "";
                    if (isset($data->mfg_date)) {
                        $mfg_date = date('d/m/Y', strtotime($data->mfg_date));
                    }
                    return $mfg_date;
                })
                ->addColumn("check", function ($data) {
                    return "<input type='checkbox' required='required' name='cancel_id[]' class='cancel-check' id='" . $data->id . "' value='" . $data->id . "'>";
                })
                ->rawColumns(["check"])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function submit(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $data = $request->cancel_id;
                foreach ($data as $id) {
                    $batch = outboundBatch::find($id);
                    $detail = outboundDetails::find($batch->picking_id);
                    $serial = stockLedger::find($batch->serial_id);
                    $serial->qtya = $serial->qtya + $batch->qty;
                    $serial->qtyp = $serial->qtyp - $batch->qty;
                    $serial->save();

                    $detail->picking_flag = 'No';
                    $detail->save();

                    $sum_despatch = DB::table('iv_outbound_despatch')
                        ->where('outbound_id', $batch->outbound_id)
                        ->sum('pqty');

                    DB::table('iv_outbound_despatch')
                        ->where('outbound_id', $batch->outbound_id)
                        ->update([
                            'pqty' => $sum_despatch - $batch->qty
                        ]);

                    $batch->delete();
                }

                DB::commit();

                $message = ['success' => "Done"];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
