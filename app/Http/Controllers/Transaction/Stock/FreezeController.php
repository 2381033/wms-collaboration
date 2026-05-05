<?php

namespace App\Http\Controllers\Transaction\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\Stock\Ledger as StockLedger;

class FreezeController extends Controller
{
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        if ($request->ajax()) {
            if ( $request->status == "Freeze" ) {
                $status = "No";
            } else {
                $status = "Yes";
            }

            if (!empty($request->product_from) && !empty($request->product_to)) {
                $product_from = $request->product_from;
                $product_to = $request->product_to;
            } else {
                if (!empty($request->product_from) && empty($request->product_to)) {
                    $product_from = $request->product_from;
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                } else if (empty($request->brand_code_from) && !empty($request->product_to)) {
                    $product_from = "";
                    $product_to = $request->product_to;
                } else {
                    $product_from = "";
                    $product_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                }
            }

            $site_id = "%";
            $area_id = "%";

            if (!empty($request->site_id) && isset($request->site_id)) {
                $site_id = $request->site_id;
            }

            if (!empty($request->area_id) && isset($request->area_id)) {
                $area_id = $request->area_id;
            }

            if (!empty($request->location_from) && !empty($request->location_to)) {
                $location_from = $request->location_from;
                $location_to = $request->location_to;
            } else {
                if (!empty($request->location_from) && empty($request->location_to)) {
                    $location_from = $request->location_from;
                    $location_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                } else if (empty($request->brand_code_from) && !empty($request->location_to)) {
                    $location_from = "";
                    $location_to = $request->location_to;
                } else {
                    $location_from = "";
                    $location_to = "zzzzzzzzzzzzzzzzzzzzzzzzzzzzzz";
                }
            }

            $list_data = DB::table("iv_stock_ledger as a")
                ->select('a.*', "b.product_name", "d.site_name", "e.area_name")
                ->join('iv_product as b', 'a.product_id', 'b.id')
                ->join('users_principal as c', 'a.principal_id', 'c.principal_id')
                ->leftJoin('iv_site as d', 'a.site_id', 'd.id')
                ->leftJoin('iv_site_area as e', 'a.area_id', 'e.id')
                ->where('a.company_id', $company_id)
                ->where('c.user_id', $user_id)
                ->where('a.principal_id', $request->principal_id)
                ->where("a.freeze_flag", $status)
                ->whereBetween("a.product_code", [$product_from, $product_to])
                ->where("a.site_id", "LIKE", $site_id)
                ->where(DB::raw("COALESCE(a.area_id, 0)"), "LIKE", $area_id)
                ->whereBetween(DB::raw("COALESCE(a.location_code, '')"), [$location_from, $location_to])
                ->where("a.qtya", ">", 0)
                ->get();

            return datatables()->of($list_data)
                    ->addColumn('check', function ($data) {
                        return '<input type="checkbox" required="required" name="freeze_id[]" class="freeze" id="' . $data->id . '" value="' . $data->id . '">';
                    })
                    ->rawColumns(['check'])
                    ->addIndexColumn()
                    ->make(true);
        }

        return view("transaction.freeze");
    }

    public function submit(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            $confirmed_by = Auth::user()->username;

            try {
                $data = $request->freeze_id;
                $status = $request->status;
                $remarks = $request->remarks;

                foreach ($data as $id) {
                    $ledger = StockLedger::find($id);

                    if ( $status == "Freeze" ) {
                        $ledger->freeze_flag = "Yes";
                        $ledger->freeze_by = $confirmed_by;
                        $ledger->freeze_date = \Carbon\Carbon::now();
                        $ledger->freeze_reason = $remarks;
                    } else {
                        $ledger->freeze_flag = "No";
                        $ledger->freeze_by = $confirmed_by;
                        $ledger->freeze_date = \Carbon\Carbon::now();
                        $ledger->freeze_reason = $remarks;
                    }

                    $ledger->save();
                }

                DB::commit();

                $message = ['success'=>'Data Successfully Saved'];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>$e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
