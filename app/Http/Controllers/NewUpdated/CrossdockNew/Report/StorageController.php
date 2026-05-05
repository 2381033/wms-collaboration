<?php

namespace App\Http\Controllers\NewUpdated\CrossdockNew\Report;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class StorageController extends Controller
{
    private function joinTableCargoinbound()
    {
        $data = DB::table('cross_inbound_header as header')
            ->select(
                'header.*',
                'detail.id as id_detail',
                'detail.id_header',
                'detail.p',
                'detail.l',
                'detail.t',
                'detail.qty',
                'detail.unit',
                'detail.description',
            )
            ->join('cross_inbound_detail as detail', 'detail.id_header', '=', 'header.id')
            ->where('confirmed_flag', 'confirmed')
            ->get();

        return $data;
    }

    private function joinTableCargoOutbound()
    {
        $data = DB::table('cross_outbound_header as header')
            ->join('cross_outbound_detail as detail', 'detail.id_header', '=', 'header.id')
            ->where('confirmed_flag', 'confirmed')
            ->groupBy('job_no')
            ->get();
        $data->map(function ($value) {
            $value->stock    = DB::table('cross_stock_ledger')->where('id', $value->id_stock)->first();
            $value->despatch = DB::table('cross_outbound_despatch')->where('id_header', $value->id_header)->first();
        });

        return $data;
    }

    private function getCustomer($id)
    {
        $customer = DB::table('cross_mt_customer')
            ->where('id', $id)
            ->value('name');

        return $customer;
    }

    private function getTransaction()
    {
        $data = DB::table('cross_stock_transaction')
            ->orderBy('created_at', 'ASC')
            ->get();

        return $data;
    }

    public function search(Request $request)
    {
        $tittle = 'Storage Report';
        $start = date("Y-m-d", strtotime($request->start));
        $end = date("Y-m-d", strtotime($request->end));

        $data = $this->getTransaction()
            ->where('id_branch', $request->id_branch)
            ->where('id_warehouse', $request->id_warehouse)
            ->whereBetween('created_at', [$start . '00:00:00', $end . '23:59:59']);
        if ($data->count() > 0) {
            $data->map(function ($item) {
                $item->date =  Carbon::parse($item->created_at)->format('Y-m-d');
            });
            $grouping = $data->groupBy(function ($item) {
                return Carbon::parse($item->created_at)->format('Y-m-d');
            });
            foreach ($grouping as $key => $value) {
                $list[$key] = [
                    $key => $key,
                    'in' => $data->where('date', $key)->where('type_job', 'in'),
                    'out' => $data->where('date', $key)->where('type_job', 'out'),
                ];
            }
        } else {
            Session::flash('warning', 'Data not found..');
            return back();
        }
        return view('new.CrossDock.Report.StorageNew.storage', compact('tittle', 'list'));
    }
}
