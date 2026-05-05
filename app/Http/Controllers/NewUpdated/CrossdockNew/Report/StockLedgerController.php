<?php

namespace App\Http\Controllers\NewUpdated\CrossdockNew\Report;

use App\Exports\Crossdock\StockLedgerExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class StockLedgerController extends Controller
{
    private function getHeaderInbound($id)
    {
        $data = DB::table('cross_inbound_header')
            ->where('id', $id)
            ->first();

        return $data;
    }

    private function getCustomer($id)
    {
        $customer = DB::table('cross_mt_customer')
            ->where('id', $id)
            ->value('name');

        return $customer;
    }


    private function getWarehouse()
    {
        $data = DB::table('cross_user_warehouse')
            ->where('id_user', Auth::user()->id)
            ->get()->pluck('id_warehouse')->toArray();
        $data = DB::table('cross_mt_warehouse')
            ->whereIn('id', $data)
            ->get();

        return $data;
    }

    public function search(Request $request)
    {
        $type = str_replace("-", '_', $request->type);
        $data = DB::table('cross_' . $type)
            ->where('id_branch', $request->id_branch)
            ->where('id_warehouse', $request->id_warehouse)
            ->get();

        $id_customer = is_numeric($request->id_customer);
        if ($id_customer) {
            $data = $data->where('id_customer', $request->id_customer);
            $id_customer = $request->id_customer;
        } else {
            $id_customer = 0;
        }

        $tittle = '';
        $report_type = $request->report_type;
        if ($data->count() > 0) {
            //jika pdf
            if ($request->has('print')) {
                $tittle = 'Stock Ledger Report ' . '(' . ucwords($report_type) . ')';
                $data->where('on_hand', '>', 0);
                $data->map(function ($value) {
                    $value->header = $this->getHeaderInbound($value->id_inbound);
                    $value->warehouse = $this->getWarehouse($value->id_warehouse)->first()->name ?? '-';
                    $value->customer = $this->getCustomer($value->id_customer);
                });

                $groupBy = $data->groupBy('id_customer');
                foreach ($groupBy as $key => $value) {
                    foreach ($value->where('id_customer', $key) as $k => $v) {
                        $w[$key][] = $v->on_hand * $v->w;
                        $cbm[$key][] = $v->on_hand * $v->cbm_per_unit;
                    }
                    $w_sum[$key] = array_sum($w[$key]);
                    $cbm_sum[$key] = array_sum($cbm[$key]);
                    $customer[$key] = $value->first()->customer ?? '-';
                    $warehouse[$key] = $value->first()->warehouse ?? '-';
                    $total_sku[$key] = array_sum($value->pluck('on_hand')->toArray());
                }

                return view('new.CrossDock.Report.' . $type . '.index', compact('data', 'tittle', 'report_type', 'id_customer', 'groupBy', 'customer', 'warehouse', 'total_sku', 'w_sum', 'cbm_sum'));
            }
            //jika excel
            else {
                if (is_numeric($id_customer)) {
                    $filename = 'Stock-' . $this->getCustomer($id_customer) . '.xlsx';
                } else {
                    $filename = 'Stock - ALL CUSTOMER' . '.xlsx';
                }
                return Excel::download(new StockLedgerExport($request->id_branch, $id_customer, $request->id_warehouse), $filename);
            }
        } else {
            Session::flash('warning', 'Data Not Found..');
            return back();
        }
    }
}
