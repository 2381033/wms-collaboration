<?php

namespace App\Http\Controllers\Transaction\CycleCount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Product as MasterProduct;
use App\Models\Transaction\CycleCount\Detail as CycleCountDetail;

class DetailController extends Controller
{
    public function index(Request $request) {
        $this->menu_name = "Cycle-Count";

        $details = [];
        if ($request->ajax()) {
            if (!empty($request->cycle_id) && !empty($request->cycle_id)) {
                $details = DB::table("iv_cyclecount_detail as a")
                                ->select("a.*", "b.product_name", "c.site_name", "d.area_name", "b.unit_level")
                                ->join("iv_product as b", "a.product_id", "b.id")
                                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                                ->where("a.cyclecount_id", $request->cycle_id)
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
            ->editColumn("actual_pqty", function ($data)
            {
                $input = "<input type='hidden' value='" . $data->id . "' name='id[]' class='form-control'/><input type='text' value='$data->actual_pqty' name='actual_pqty[]' class='form-control' style='width:70px;'/>";

                return $input;
            })
            ->editColumn("actual_mqty", function ($data)
            {
                $disabled = "";
                if ($data->unit_level == 1) {
                    $disabled = "readonly";
                } else if ($data->unit_level == 2) {
                    $disabled = "readonly";
                }

                $input = "<input type='text' value='$data->actual_mqty' name='actual_mqty[]' class='form-control' style='width:70px;' $disabled/>";
                return $input;
            })
            ->editColumn("actual_bqty", function ($data)
            {
                $disabled = "";
                if ($data->unit_level == 1) {
                    $disabled = "readonly";
                }

                $input = "<input type='text' value='$data->actual_bqty' name='actual_bqty[]' class='form-control' style='width:70px;' $disabled/>";
                return $input;
            })
            ->rawColumns(["actual_pqty", "actual_mqty", "actual_bqty"])
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

                for ($i=0; $i < count($id) ; $i++) {
                    $detail = CycleCountDetail::find($id[$i]);
                    $product = MasterProduct::find($detail->product_id);

                    $qty = ($actual_pqty[$i] * $product->uppp ) + ($actual_mqty[$i] * $product->muppp ) + $actual_bqty[$i];

                    $detail->actual_pqty = $actual_pqty[$i];
                    $detail->actual_mqty = $actual_mqty[$i];
                    $detail->actual_bqty = $actual_bqty[$i];
                    $detail->actual_qty = $qty;
                    $detail->save();
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
