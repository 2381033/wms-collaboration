<?php

namespace App\Http\Controllers\NewUpdated\CrossdockNew\Report;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class DailyInOutController extends Controller
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
                'detail.w',
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

    public function search(Request $request)
    {
        $start = $request->start;
        $start = date("Y-m-d", strtotime($start));
        $end = $request->end;
        $end = date("Y-m-d", strtotime($end));
        $type = $request->type;
        $id_header = DB::table('cross_' . $type . '_detail')
            ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->groupBy('id_header')
            ->get()
            ->pluck('id_header')
            ->toArray();
        $tittle = '';
        if ($type == 'inbound') {
            $tittle = 'Inbound Report (Summary)';
            $data = $this->joinTableCargoInbound()
                ->where('id_branch', $request->id_branch)
                ->where('id_warehouse', $request->id_warehouse)
                ->whereIn('id_header', $id_header);
            if ($data->count() == 0) {
                Session::flash('warning', 'Data not found..');
                return back();
            }
            $groupBy = $data->groupBy('job_no');
            foreach ($data as $key => $value) {
                $cbm[$value->job_no][] = $value->p * $value->l * $value->t * $value->qty / 1000000;
                $qty[$value->job_no][] = $value->qty;
                $weight[$value->job_no][] = $value->qty * $value->w;
            }

            $list = [];
            foreach ($groupBy as $key => $value) {
                $list[] = [
                    'job_no' => $key,
                    'date_in' => $data->where('job_no', $key)->first()->created_at ?? '-',
                    'vehicle_number' => $data->where('job_no', $key)->first()->vehicle_number ?? '-',
                    'container_number' => $data->where('job_no', $key)->first()->container_number ?? '-',
                    'transporter_name' => $data->where('job_no', $key)->first()->transporter_name ?? '-',
                    'vehicle' => $data->where('job_no', $key)->first()->vehicle ?? '-',
                    'size' => $data->where('job_no', $key)->first()->size ?? '-',
                    'shipment_arrival_date' => $data->where('job_no', $key)->first()->shipment_arrival_date ?? '-',
                    'unloading_start' => $data->where('job_no', $key)->first()->unloading_start ?? '-',
                    'unloading_finish' => $data->where('job_no', $key)->first()->unloading_finish ?? '-',
                    'sku' => $data->where('job_no', $key)->first()->sku ?? '-',
                    'description' => $data->where('job_no', $key)->first()->description ?? '-',
                    'customer' => $this->getCustomer($data->where('job_no', $key)->first()->id_customer ?? 0),
                    'cbm_total' => array_sum($cbm[$key]),
                    'qty_total' => array_sum($qty[$key]),
                    'w_total' => array_sum($weight[$key]),
                ];
            }
            if (count($list) < 1) {
                Session::flash('warning', 'Data Not Found..');
                return back();
            }
        } else {
            $tittle = 'Outbound Report (Summary)';
            $data = $this->joinTableCargoOutbound()
                ->where('id_branch', $request->id_branch)
                ->where('id_warehouse', $request->id_warehouse)
                ->whereIn('id_header', $id_header);
            if ($data->count() == 0) {
                Session::flash('warning', 'Data not found..');
                return back();
            }
            $groupBy = $data->groupBy('job_no');
            foreach ($data as $key => $value) {
                $cbm[$value->job_no][] = $value->stock->p * $value->stock->l * $value->stock->t * $value->qty / 1000000;
                $qty[$value->job_no][] = $value->qty;
                $weight[$value->job_no][] = $value->qty * $value->stock->w;
            }

            foreach ($groupBy as $key => $value) {
                $list[] = [
                    'job_no' => $key,
                    'date_out' => $data->where('job_no', $key)->first()->created_at ?? '-',
                    'vehicle_number' => $data->where('job_no', $key)->first()->despatch->vehicle_no ?? '-',
                    'container_number' => $data->where('job_no', $key)->first()->despatch->container_no ?? '-',
                    'vehicle_type' => $data->where('job_no', $key)->first()->despatch->vehicle_type ?? '-',
                    'vehicle_size' => $data->where('job_no', $key)->first()->despatch->vehicle_size ?? '-',
                    'shipment_arrival_date' => $data->where('job_no', $key)->first()->shipment_arrival_date ?? '-',
                    'unloading_start' => $data->where('job_no', $key)->first()->loading_start ?? '-',
                    'unloading_finish' => $data->where('job_no', $key)->first()->loading_finish ?? '-',
                    'sku' => $data->where('job_no', $key)->first()->sku ?? '-',
                    'description' => $data->where('job_no', $key)->first()->description ?? '-',
                    'customer' => $this->getCustomer($data->where('job_no', $key)->first()->id_customer ?? 0),
                    'cbm_total' => number_format(array_sum($cbm[$key]), 2, ',', '.'),
                    'qty_total' => array_sum($qty[$key]),
                    'w_total' => number_format(array_sum($weight[$key]), 0, ',', '.'),
                ];
            }
            // dd($list);
        }
        $data->map(function ($value) {
            $value->customer = $this->getCustomer($value->id_customer);
        });
        return view('new.CrossDock.Report.Daily.' . $type, compact('data', 'list', 'tittle'));
    }
}
