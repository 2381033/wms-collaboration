<?php

namespace App\Http\Controllers\Transaction\StockTake;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Transaction\StockTake\Detail as StockTakeDetail;
use App\Models\Master\Product as MasterProduct;

class DetailController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table("iv_stocktake_detail as a")
                ->select("a.*", "b.product_name", "c.site_name", "d.area_name", "b.unit_level")
                ->join("iv_product as b", "a.product_id", "b.id")
                ->leftjoin("iv_site as c", "a.site_id", "c.id")
                ->leftjoin("iv_site_area as d", "a.area_id", "d.id")
                ->where("a.stocktake_id", $request->take_id)
                ->where("a.confirmed_flag", "No")
                ->where("a.qty", '>', 0);

            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('b.product_name', 'like', "%{$search}%")
                        ->orWhere('a.product_code', 'like', "%{$search}%")
                        ->orWhere('a.lot_no', 'like', "%{$search}%")
                        ->orWhere('a.location_code', 'like', "%{$search}%")
                        ->orWhere('c.site_name', 'like', "%{$search}%")
                        ->orWhere('d.area_name', 'like', "%{$search}%");
                });
            }

            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            $skip = ($page - 1) * $perPage;

            $total = $query->count();

            $details = $query->skip($skip)->take($perPage)->get();

            return response()->json([
                'data' => $details,
                'total' => $total,
                'page' => (int)$page,
                'per_page' => (int)$perPage,
            ]);
        }
    }


    public function store(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $id = $request->id;
                $actual_pqty = $request->actual_pqty;
                $actual_mqty = $request->actual_mqty;
                $actual_bqty = $request->actual_bqty;
                $actual_lot_no = $request->actual_lot_no;
                $actual_mfg_date = $request->actual_mfg_date;
                $actual_exp_date = $request->actual_exp_date;

                for ($i = 0; $i < count($id); $i++) {
                    $detail = StockTakeDetail::find($id[$i]);
                    $product = MasterProduct::find($detail->product_id);

                    $qty = ($actual_pqty[$i] * $product->uppp) + ($actual_mqty[$i] * $product->muppp) + $actual_bqty[$i];

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

                $message = ["success" => "Sukses"];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function updateList($id, Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $id = $request->id;
                $actual_pqty = $request->actual_pqty;
                // $actual_mqty = $request->actual_mqty;
                // $actual_bqty = $request->actual_bqty;
                // $actual_lot_no = $request->actual_lot_no;
                // $actual_mfg_date = $request->actual_mfg_date;
                // $actual_exp_date = $request->actual_exp_date;

                $detail = StockTakeDetail::find($id);
                $product = MasterProduct::find($detail->product_id);

                // $qty = ($actual_pqty[$i] * $product->uppp) + ($actual_mqty[$i] * $product->muppp) + $actual_bqty[$i];

                // $mfg = null;
                // $mfg_date = null;
                // if (!empty($actual_mfg_date[$i])) {
                //     $mfg = \Carbon\Carbon::createFromFormat('d/m/Y', $actual_mfg_date[$i]);
                //     $mfg_date = \Carbon\Carbon::parse($mfg)->format('Y-m-d');
                // }

                // $expired = null;
                // $exp_date = null;
                // if (!empty($actual_exp_date[$i])) {
                //     $expired = \Carbon\Carbon::createFromFormat('d/m/Y', $actual_exp_date[$i]);
                //     $exp_date = \Carbon\Carbon::parse($expired)->format('Y-m-d');
                // }

                $detail->actual_pqty = $actual_pqty;
                $detail->actual_qty = $actual_pqty;
                $detail->notes = $request->note;
                $detail->confirmed_flag = 'Yes';
                $detail->confirmed_by = Auth::user()->username;
                $detail->confirmed_date = date('Y-m-d H:i:s');
                $detail->save();
                // $detail->actual_mqty = $actual_mqty[$i];
                // $detail->actual_bqty = $actual_bqty[$i];
                // $detail->actual_lot_no = $actual_lot_no[$i];
                // $detail->actual_mfg_date = $mfg_date;
                // $detail->actual_exp_date = $exp_date;

                DB::commit();

                $message = ["success" => "Sukses"];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function matchingList($id)
    {
        $exception = DB::transaction(function () use ($id) {
            try {
                $detail = StockTakeDetail::find($id);
                DB::table('iv_stocktake_detail')
                    ->where('id', $id)
                    ->update([
                        'actual_pqty' => $detail->pqty,
                        'actual_qty' => $detail->qty,
                        'confirmed_flag' => 'Yes',
                        'confirmed_by' => Auth::user()->username,
                        'confirmed_date' => date('Y-m-d H:i:s')
                    ]);
                DB::commit();
                $message = ["success" => "Sukses"];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ["error" => [$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
