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
        $type = str_replace('-', '_', $request->type);

        $baseQuery = DB::table('cross_' . $type . ' as c')
            ->join('cross_mt_warehouse as w', 'w.id', '=', 'c.id_warehouse')
            ->join('cross_mt_customer as cu', 'cu.id', '=', 'c.id_customer')
            ->leftJoin('cross_inbound_header as ih', 'ih.id', '=', 'c.id_inbound')
            ->where('c.id_branch', $request->id_branch)
            ->where('c.id_warehouse', $request->id_warehouse)
            ->where('c.on_hand', '>', 0);

        if (is_numeric($request->id_customer)) {
            $baseQuery->where('c.id_customer', $request->id_customer);
            $id_customer = $request->id_customer;
        } else {
            $id_customer = 0;
        }

        $data = (clone $baseQuery)
            ->select([
                'c.job_no',
                'c.id_cargo',
                'c.on_hand',
                'c.on_actual',
                'c.on_booking',
                'c.p',
                'c.l',
                'c.t',
                'c.w',
                'c.cbm_per_unit',
                'c.unit',
                'c.created_at',
                'cu.name as customer',
                'w.name as warehouse',
                'ih.remarks as inbound_remark',
            ])
            ->orderBy('cu.name')
            ->orderBy('c.sku')
            ->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('warning', 'Data Not Found');
        }

        $summary = (clone $baseQuery)
            ->selectRaw('
                c.id_customer,
                cu.name as customer,
                w.name as warehouse,
                COUNT(DISTINCT c.sku) as total_item,
                SUM(c.on_hand) as on_hand,
                SUM(c.on_booking) as on_booking,
                SUM(c.on_actual) as on_actual,
                SUM(c.w) as w_sum,
                SUM(c.on_hand * c.cbm_per_unit) as total_cbm
            ')
            ->groupBy('c.id_customer', 'cu.name', 'w.name')
            ->orderBy('cu.name')
            ->get();
        if ($request->has('excel')) {
            if ($data->isEmpty()) {
                return redirect()->back()->with('warning', 'Data Not Found');
            } else {
                if ($id_customer) {
                    $customerName = $this->getCustomer($id_customer);
                    $filename = 'Stock-' . $customerName . '.xlsx';
                } else {
                    $filename = 'Stock-ALL-CUSTOMER.xlsx';
                }
                return Excel::download(
                    new StockLedgerExport(
                        $request->id_branch,
                        $request->id_warehouse,
                        $request->id_customer,
                        $request->report_type,
                    ),
                    $filename
                );
            }
        } else {
            return view('new.CrossDock.Report.' . $type . '.index', [
                'data'         => $data,
                'summary'      => $summary,
                'id_customer'  => $id_customer,
                'report_type'  => $request->report_type,
                'tittle'       => 'Stock Ledger Report (' . ucwords($request->report_type) . ')'
            ]);
        }
    }
}
