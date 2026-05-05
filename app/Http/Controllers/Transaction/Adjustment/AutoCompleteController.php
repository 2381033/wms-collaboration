<?php

namespace App\Http\Controllers\Transaction\Adjustment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Adjustment\Job as AdjustmentJob;

class AutoCompleteController extends Controller
{
    public function stockList(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $adjustment_id = $request->adjustment_id;
        $principal_id = $request->principal_id;

        $adjust = AdjustmentJob::find($adjustment_id);

        if ($request->ajax()) {
            $stock = DB::table("iv_stock_ledger as a")
                ->select(
                    DB::raw("convert((a.qtya  - (a.qtya % d.uppp)) / d.uppp, int) as pqty"),
                    DB::raw("convert(((a.qtya % d.uppp) - ((a.qtya % d.uppp) % d.muppp)) / d.muppp, int) as mqty"),
                    DB::raw("a.qtya % d.uppp % d.muppp as bqty"),
                    "d.puom",
                    "d.muom",
                    "d.buom",
                    "a.lot_no",
                    "a.exp_date",
                    "a.mfg_date",
                    "a.product_code",
                    "d.product_name",
                    "e.site_name",
                    "f.area_name",
                    "a.location_code",
                    "a.id",
                    "a.serial_no"
                )
                ->join("users_site as b", "a.site_id", "b.site_id")
                ->join("users_principal as c", "a.principal_id", "c.principal_id")
                ->join("iv_product as d", "a.product_id", "d.id")
                ->leftjoin("iv_site as e", "a.site_id", "e.id")
                ->leftjoin("iv_site_area as f", "a.area_id", "f.id")
                ->where("b.user_id", "=", $user_id)
                ->where("c.user_id", "=", $user_id)
                ->where("a.company_id", "=", $company_id)
                ->where("a.principal_id", "=", $principal_id)
                ->where("a.branch_id", "=", $adjust->branch_id)
                ->where("a.qtya", ">", 0)
                ->where("a.freeze_flag", "=", "No")
                ->get();

            return datatables()->of($stock)
                ->editColumn("exp_date", function ($data) {
                    return date("d/m/Y", strtotime($data->exp_date));
                })
                ->editColumn("mfg_date", function ($data) {
                    return date("d/m/Y", strtotime($data->mfg_date));
                })
                ->addColumn("action", function ($data) {
                    $button = "";
                    $button .= "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='" . $data->id . "' data-original-title='Edit' class='edit btn btn-info btn-sm edit-stock'><i class='far fa-check-square'></i></a>";
                    return $button;
                })
                ->rawColumns(["action"])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function stockEdit(Request $request)
    {
        $list = DB::table("iv_stock_ledger as a")
            ->select(
                "a.id",
                "a.product_id",
                "c.product_name",
                "a.lot_no",
                "a.po_number",
                "a.mfg_date",
                "a.exp_date",
                DB::raw("convert((a.qtya  - (a.qtya % c.uppp)) / c.uppp, int) as pqty"),
                DB::raw("convert(((a.qtya % c.uppp) - ((a.qtya % c.uppp) % c.muppp)) / c.muppp, int) as mqty"),
                DB::raw("a.qtya % c.uppp % c.muppp as bqty"),
                "c.puom",
                "c.muom",
                "c.buom",
                "c.uppp",
                "c.muppp",
                "a.site_id",
                "d.site_name",
                "a.area_id",
                "e.area_name",
                "a.location_id",
                "a.location_code",
                "a.principal_id",
                "b.principal_name",
                "a.manufactur_id",
                "f.manufactur_name",
                "a.status_id",
                "g.status_name",
                "c.unit_level"
            )
            ->join("iv_principal as b", "a.principal_id", "b.id")
            ->join("iv_product as c", "a.product_id", "c.id")
            ->leftjoin("iv_site as d", "a.site_id", "d.id")
            ->leftjoin("iv_site_area as e", "a.area_id", "e.id")
            ->leftjoin("iv_manufactur as f", "a.manufactur_id", "f.id")
            ->leftjoin("iv_stock_status as g", "a.status_id", "g.id")
            ->where("a.id", "=", $request->id)
            ->first();

        return response()->json($list);
    }

    public function productList(Request $request)
    {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;
        $principal_id = $request->principal_id;

        if ($request->ajax()) {
            $stock = DB::table("iv_product as a")
                ->select("a.*")
                ->join("users_principal as b", "a.principal_id", "b.principal_id")
                ->where("a.company_id", "=", $company_id)
                ->where("a.principal_id", "=", $principal_id)
                ->where("b.user_id", "=", $user_id)
                ->where("a.active", "=", "Yes")
                ->get();

            return datatables()->of($stock)
                ->addColumn("action", function ($data) {
                    $button = "";
                    $button .= "<a href='javascript:void(0)' data-toggle='tooltip'  data-id='" . $data->id . "' data-original-title='Edit' class='edit btn btn-info btn-sm edit-product'><i class='far fa-check-square'></i></a>";
                    return $button;
                })
                ->rawColumns(["action"])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function productEdit(Request $request)
    {
        $list = DB::table("iv_product as a")
            ->select("a.*", "b.principal_name")
            ->join("iv_principal as b", "a.principal_id", "b.id")
            ->where("a.id", "=", $request->id)
            ->first();

        return response()->json($list);
    }
}
