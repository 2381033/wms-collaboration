<?php

namespace App\Http\Controllers\Transaction\StockTake;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\StockTake\Detail as StockTakeDetail;
use App\Models\Master\Product as MasterProduct;

class DetailController extends Controller
{
    public function index(Request $request) {
        $details = [];
        if ($request->ajax()) {
            if (!empty($request->take_id) && !empty($request->take_id)) {
                $details = DB::table("iv_stocktake_detail as a")
                                ->select("a.*", "b.product_name", "c.site_name", "d.area_name", "b.unit_level")
                                ->join("iv_product as b", "a.product_id", "b.id")
                                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                                ->where("a.stocktake_id", $request->take_id)
                                ->where("a.confirmed_flag", "No")
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
            ->editColumn("actual_lot_no", function ($data)
            {
                return "<input type='text' value='$data->actual_lot_no' name='actual_lot_no[]' class='form-control' style='width:150px;'/>";
            })
            ->editColumn("actual_exp_date", function ($data)
            {
                $exp_date = "";
                if (!empty($data->actual_exp_date) || isset($data->actual_exp_date)) {
                    $exp_date = date('d/m/Y', strtotime($data->actual_exp_date));
                }
                return "<input type='text' value='$exp_date' name='actual_exp_date[]' class='form-control datepicker' style='width:120px;'/>";
            })
            ->editColumn("actual_mfg_date", function ($data)
            {
                $mfg_date = "";
                if (!empty($data->actual_mfg_date) || isset($data->actual_mfg_date)) {
                    $mfg_date = date('d/m/Y', strtotime($data->actual_mfg_date));
                }
                return "<input type='text' value='$mfg_date' name='actual_mfg_date[]' class='form-control datepicker' style='width:120px;'/>";
            })
            ->editColumn("actual_pqty", function ($data)
            {
                return "<input type='hidden' value='$data->id' name='id[]' class='form-control'/>
                <input type='text' value='$data->actual_pqty' name='actual_pqty[]' class='form-control' style='width:70px;'/>";
            })
            ->editColumn("actual_mqty", function ($data)
            {
                $disabled = "";
                if ($data->unit_level == 1) {
                    $disabled = "readonly";
                } else if ($data->unit_level == 2) {
                    $disabled = "readonly";
                }

                return "<input type='text' value='$data->actual_mqty' name='actual_mqty[]' class='form-control' style='width:70px;' $disabled/>";
            })
            ->editColumn("actual_bqty", function ($data)
            {
                $disabled = "";
                if ($data->unit_level == 1) {
                    $disabled = "readonly";
                }

                return "<input type='text' value='$data->actual_bqty' name='actual_bqty[]' class='form-control' style='width:70px;' $disabled/>";
            })
            ->rawColumns(["actual_pqty", "actual_mqty", "actual_bqty", "actual_lot_no", "actual_mfg_date", "actual_exp_date"])
            ->addIndexColumn()
            ->make(true);
        }
    }

    public function store(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $id = $request->id;
                $actual_pqty = $request->actual_pqty;
                $actual_mqty = $request->actual_mqty;
                $actual_bqty = $request->actual_bqty;
                $actual_lot_no = $request->actual_lot_no;
                $actual_mfg_date = $request->actual_mfg_date;
                $actual_exp_date = $request->actual_exp_date;

                for ($i=0; $i < count($id) ; $i++) {
                    $detail = StockTakeDetail::find($id[$i]);
                    $product = MasterProduct::find($detail->product_id);

                    $qty = ($actual_pqty[$i] * $product->uppp ) + ($actual_mqty[$i] * $product->muppp ) + $actual_bqty[$i];

                    $mfg = null;
                    $mfg_date = null;
                    if (!empty($actual_mfg_date[$i])) {
                        $mfg = \Carbon\Carbon::createFromFormat('d/m/Y', $actual_mfg_date[$i]);
                        $mfg_date = \Carbon\Carbon::parse($mfg)->format('Y-m-d');
                    }

                    $expired = null;
                    $exp_date = null;
                    if (!empty($actual_exp_date[$i])) {
                        $expired = \Carbon\Carbon::createFromFormat('d/m/Y', $actual_exp_date[$i]);
                        $exp_date = \Carbon\Carbon::parse($expired)->format('Y-m-d');
                    }

                    $detail->actual_pqty = $actual_pqty[$i];
                    $detail->actual_mqty = $actual_mqty[$i];
                    $detail->actual_bqty = $actual_bqty[$i];
                    $detail->actual_qty = $qty;
                    $detail->actual_lot_no = $actual_lot_no[$i];
                    $detail->actual_mfg_date = $mfg_date;
                    $detail->actual_exp_date = $exp_date;
                    $detail->save();
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
