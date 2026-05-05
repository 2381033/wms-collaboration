<?php

namespace App\Http\Controllers\Transaction\Replenish;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Master\Location as MasterLocation;

class ProcessController extends Controller
{
    public function index(Request $request) {
        $company_id = Auth::user()->company_id;
        $user_id = Auth::user()->id;

        $site = $request->site_id;
        if (is_null($request->site_id) || empty($request->site_id)) {
            $site = '%';
        }

        $area = $request->area_id;
        if (is_null($request->area_id) || empty($request->area_id)) {
            $area = '%';
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

        if (!empty($request->location_from) && !empty($request->location_to)) {
            $location_from = $request->location_from;
            $location_to = $request->location_to;
        } else {
            if (!empty($request->location_from) && empty($request->location_to)) {
                $location_from = $request->location_from;
                $location_to = "zzzzzzzzzzzzzzz";
            } else if (empty($request->brand_code_from) && !empty($request->location_to)) {
                $location_from = "";
                $location_to = $request->location_to;
            } else {
                $location_from = "";
                $location_to = "zzzzzzzzzzzzzzz";
            }
        }

        if ($request->ajax()) {
            $list = MasterLocation::from('iv_location as a')
                                ->select('a.id', 'b.product_name', 'c.site_name', 'd.area_name', 'a.location_code', 'b.puom', 'a.reorder_level', 'a.reorder_qty', DB::raw('CASE WHEN e.qtya is null THEN 0 ELSE e.qtya END as qtya'))
                                ->join('iv_product as b', 'a.product_id', 'b.id')
                                ->join('iv_site as c', 'a.site_id', 'c.id')
                                ->join('iv_site_area as d', 'a.area_id', 'd.id')
                                ->leftjoin('iv_stock_ledger as e', function($join){
                                    $join->on('a.id', '=', 'e.location_id')
                                    ->where(DB::raw('CASE WHEN e.qtys = e.qtya THEN 1 ELSE 0 END'), '=', 1)
                                    ->where('e.qtya', '>', 0);
                                })
                                ->join('users_principal as f', 'a.principal_id', 'f.principal_id')
                                ->join('users_site as g', 'a.site_id', 'g.site_id')
                                ->where('a.company_id', '=', $company_id)
                                ->where('f.user_id', $user_id)
                                ->where('g.user_id', $user_id)
                                ->where('a.principal_id', '=', $request->principal_id)
                                ->whereBetween('b.product_code', [$product_from, $product_to])
                                ->where('a.site_id', 'like', $site)
                                ->where('a.area_id', 'like', $area)
                                ->where('a.status_code', '=', 'P')
                                ->whereBetween('a.location_code', [$location_from, $location_to])
                                ->get();

            return datatables()->of($list)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" name="entry_id[]" class="entry-check" id="' . $data->id . '" value="' . $data->id . '">';
            })
            ->rawColumns(['check'])
            ->addIndexColumn()
            ->make(true);
        }
    }
}
