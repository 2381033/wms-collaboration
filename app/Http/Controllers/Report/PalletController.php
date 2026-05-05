<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Symfony\Component\VarDumper\Cloner\Data;

class PalletController extends Controller
{
    public function index()
    {
        return view("report.pallet-tag.index");
    }

    public function getLocation($site_id)
    {
        $data = DB::table('iv_location')
            ->where('site_id', $site_id)
            ->where('active', 'Yes')
            ->get();
        return response()->json(['data' => $data]);
    }

    public function print(Request $request)
    {
        $data = DB::table('iv_stock_ledger')
            ->select('job_no')
            ->where('principal_id', $request->principal_id)
            ->where('product_code', $request->product_code_from)
            ->where('site_id', $request->site_id)
            ->where('location_id', $request->location_code)
            ->where('qtys', '>', 0)
            ->get();
        if (!is_null($request->lot_no)) {
            $data = $data->where('lot_no', $request->lot_no);
        }
        $principal = DB::table('iv_principal')
            ->where('id', $request->principal_id)
            ->value('principal_name');
        $job_no = $data->pluck('job_no')->toArray();
        $detail = DB::table('iv_inbound_detail')
            ->select(
                'id',
                'mfg_date',
                'exp_date',
                'lot_no',
                'product_id',
                'inbound_id',
                'product_status',
                'product_code'
            )
            ->whereIn('job_no', $job_no)
            ->where('product_code', $request->product_code_from)
            ->get();

        $data = DB::table('iv_inbound_per_pallet')
            ->where('product_code', $request->product_code_from)
            // ->where('location_id', $request->location_code)
            ->whereIn('picking_id', $detail->pluck('id')->ToArray())
            ->get();
        dd($data->pluck('location_code')->toArray());
        $data = $data->map(function ($value) use ($detail) {
            $value->master_detail = $detail
                ->where('id', $value->picking_id)
                ->first();
            return $value;
        });

        $data = $data->map(function ($value) {
            $value->master_product = DB::table("iv_product")
                ->select('product_name')
                ->where('id', $value->master_detail->product_id)
                ->first();
            return $value;
        });

        $data = $data->map(function ($value) {
            $value->master_job = DB::table("iv_inbound_job")
                ->select('job_no', 'job_date')
                ->where('id', $value->master_detail->inbound_id)
                ->first();
            return $value;
        });

        return view("report.pallet-tag.pallet", compact('data', 'principal'));
    }
}
