<?php

namespace App\Http\Controllers\Transaction\Adjustment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Master\Location as MasterLocation;
use App\Models\Transaction\Adjustment\Batch as AdjustmentBatch;
use App\Models\Transaction\Adjustment\Detail as AdjustmentDetail;

class CancelController extends Controller
{
    public function index(Request $request) {
        $detail = [];
        if ($request->ajax()) {
            if (!empty($request->adjust_id) && !empty($request->adjust_id)) {
                $detail = DB::table('iv_adjustment_detail as a')
                        ->select('a.*', 'b.principal_name', 'c.product_name', 'd.site_name', 'e.area_name')
                        ->join('iv_principal as b', 'a.principal_id', 'b.id')
                        ->join('iv_product as c', 'a.product_id', 'c.id')
                        ->join('iv_site as d', 'a.site_id', 'd.id')
                        ->join('iv_site_area as e', 'a.area_id', 'e.id')
                        ->where('a.adjust_id', '=', $request->adjust_id)
                        ->where('a.picked_flag', '=', 'Yes')
                        ->where('a.confirmed_flag', '=', 'No')
                        ->get();
            }

            return datatables()->of($detail)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" required="required" name="cancel_id[]" class="cancel-check" id="' . $data->id . '" value="' . $data->id . '">';
            })
            ->editColumn('exp_date', function ($data)
            {
                return date('d/m/Y', strtotime($data->exp_date) );
            })
            ->editColumn('mfg_date', function ($data)
            {
                return date('d/m/Y', strtotime($data->mfg_date) );
            })
            ->rawColumns(['check'])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function submit(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $data = $request->cancel_id;

                foreach ($data as $id) {
                    $detail = AdjustmentDetail::find($id);

                    if ($detail->status_flag == 'Exist') {
                        if ($detail->adjust_type == 'Minus') {
                            $serial = StockLedger::find($detail->serial_id);

                            $serial->qtya = $serial->qtya + $detail->actual_qty;
                            $serial->qtyp = $serial->qtyp - $detail->actual_qty;
                            $serial->save();
                        }
                    } else {
                        if ($detail->adjust_type == 'Plus') {
                            $location = MasterLocation::find($detail->location_id);

                            if ($location->status_code == 'R') {
                                $location->status_code = 'E';
                                $location->save();
                            }
                        }
                    }

                    $detail->picked_flag = 'No';
                    $detail->save();

                    AdjustmentBatch::where('adjust_id', '=', $detail->adjust_id)
                            ->where('line_id', '=', $id)->delete();

                }

                DB::commit();

                $message = ["success"=>"Successfully."];

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
