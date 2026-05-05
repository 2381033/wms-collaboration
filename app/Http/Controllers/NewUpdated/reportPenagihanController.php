<?php

namespace App\Http\Controllers\NewUpdated;

use App\Http\Controllers\Controller;
use App\Models\Transaction\Stock\Ledger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Str;
use Session;
use DataTables;
use Illuminate\Support\Carbon;

class reportPenagihanController extends Controller
{
    public function index()
    {

        $jumlah_hari = Carbon::now()->month()->daysInMonth;
        $mt_in = DB::table('iv_inbound_batch as a')
            ->whereNotNull('confirmed_by')
            ->whereMonth('created_at', 12)
            ->whereYear('created_at', 2022)
            ->where('principal_id', 3)
            ->where('site_id', 1)
            ->get()->groupBy(function ($data) {
                return \Carbon\Carbon::parse($data->confirmed_date)->format('Y-m-d');
            });
        $incoming = [];
        foreach ($mt_in as $key => $value) {
            $incoming[] = DB::table('iv_inbound_batch as a')
                ->select(
                    DB::raw('SUM(length) * SUM(width) * SUM(height) as cbm'),
                )
                ->join('iv_product as b', 'b.id', '=', 'a.product_id')
                ->whereNotNull('confirmed_by')
                ->where('site_id', 1)
                ->where('a.principal_id', 3)
                ->whereDate('confirmed_date', $key)
                ->get();
        }

        $mt_out = DB::table('iv_outbound_batch as a')
            ->whereNotNull('confirmed_by')
            ->whereMonth('created_at', 12)
            ->whereYear('created_at', 2022)
            ->where('principal_id', 3)
            ->where('site_id', 1)
            ->get()->groupBy(function ($data) {
                return \Carbon\Carbon::parse($data->confirmed_date)->format('Y-m-d');
            });
        $outgoing = [];
        foreach ($mt_out as $key => $value) {
            $outgoing[] = DB::table('iv_outbound_batch as a')
                ->select(
                    DB::raw('SUM(length) * SUM(width) * SUM(height) as cbm'),
                )
                ->join('iv_product as b', 'b.id', '=', 'a.product_id')
                ->whereNotNull('confirmed_by')
                ->where('site_id', 1)
                ->where('a.principal_id', 3)
                ->whereDate('confirmed_date', $key)
                ->get();
        }

        return view("new.reportPenagihan.index", compact('incoming', 'outgoing', 'jumlah_hari'));
    }
}
