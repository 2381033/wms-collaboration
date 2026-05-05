<?php

namespace App\Http\Controllers\Transaction\Transfer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Stock\Ledger as StockLedger;
use App\Models\Transaction\Transfer\Detail as TransferDetail;
use App\Models\Transaction\Transfer\Batch as TransferBatch;
use App\Models\Master\Location as MasterLocation;

class CancelController extends Controller
{
    public function index(Request $request) {
        $this->menu_name = "Transfer";

        $detail = [];
        if ($request->ajax()) {
            if (!empty($request->transfer_id) && !empty($request->transfer_id)) {
                $detail = TransferDetail::from("iv_transfer_detail as a")
                        ->select("a.*", "b.product_name", "c.site_name", "d.area_name", "e.site_name as dest_site_name", "f.area_name as dest_area_name")
                        ->join("iv_product as b", "a.product_id", "b.id")
                        ->leftjoin("iv_site as c", "a.site_id", "c.id")
                        ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                        ->leftjoin("iv_site as e", "a.dest_site_id", "e.id")
                        ->leftjoin("iv_site_area as f", "a.dest_area_id", "f.id")
                        ->where("a.transfer_id", "=", $request->transfer_id)
                        ->where("a.picked_flag", "=", "Yes")
                        ->where("a.confirmed_flag", "=", "No")
                        ->get();
            }

            return datatables()->of($detail)
            ->addColumn("check", function ($data) {
                return "<input type='checkbox' required='required' name='cancel_id[]' class='cancel-check' id='" . $data->id . "' value='" . $data->id . "'>";
            })
            ->editColumn("exp_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->exp_date) );
            })
            ->editColumn("mfg_date", function ($data)
            {
                return date("d/m/Y", strtotime($data->mfg_date) );
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
                    $detail = TransferDetail::find($id);
                    $serial = StockLedger::find($detail->serial_id);

                    $serial->qtya = $serial->qtya + $detail->actual_qty;
                    $serial->qtyp = $serial->qtyp - $detail->actual_qty;
                    $serial->save();

                    $detail->picked_flag = "No";
                    $detail->save();

                    TransferBatch::where("transfer_id", "=", $detail->transfer_id)
                                ->where("line_id", "=", $id)->delete();

                    $location = MasterLocation::find($detail->dest_location_id);

                    if ($detail->serial_id == 0) {
                        if ($location->status_code == "R") {
                            $location->status_code = "E";
                            $location->save();
                        }
                    }
                }

                DB::commit();

                $message = ["success"=>"sukses"];

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
