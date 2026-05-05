<?php

namespace App\Http\Controllers\NewUpdated\Export\BeaCukai;

use App\Exports\BeaCukai\MonthlyExport;
use App\Exports\inboundPackingExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Excel;

class ReportController extends Controller
{
      private function myBranch()
      {
            $data = DB::table('sm_user_branch')
                  ->where('user_id', Auth::user()->id)
                  ->value('branch_id');
            return $data;
      }

      public function index($type)
      {
            $auth = DB::table('auth_group')
                  ->where('id', Auth::user()->auth_group_id)
                  ->value('name');
            return view("new.Export.BeaCukai.Report." . $type, compact('auth'));
      }

      private function getMonthly($start, $end)
      {
            $tahun = explode('-', $start)[0];
            $tahun = (int) $tahun;
            if ($tahun > 2023) {
                  $data = DB::table("ex_bea_cukai as a")
                        ->orderBy('a.pkbe_date', 'ASC')
                        ->join('ex_bea_cukai_detail as b', 'a.id', 'b.id_header_bc')
                        ->whereBetween('a.pkbe_date', [$start, $end])
                        ->get();
                  $data = $data->map(function($value){
                        $value->forwarder_name = $this->detailForwarder($value->forwarder_id);
                        return $value;
                  });
            } else {
                  $data = [];
            }
            return $data;
      }

      public function monthly($start, $end)
      {
            $data = $this->getMonthly($start, $end);
            return response()->json(['data' => $data]);
      }

      public function inbound($start, $end)
      {
            $tahun = explode('-', $start)[0];
            $tahun = (int) $tahun;
            if ($tahun > 2023) {
                  $data = DB::table("ex_inbound_header as a")
                        ->select('a.*', "b.shipper_name")
                        ->join("mt_shipper as b", "a.shipper_id", "b.id")
                        ->whereBetween('a.created_at', [$start . ' 00:00:00', $end . ' 23:59:00'])
                        ->where('a.branch_id', $this->myBranch())
                        ->get();
            } else {
                  $data = [];
            }
            return Datatables::of($data)->make(true);
      }

      public function icr($id)
      {
            $header = DB::table("ex_inbound_header as a")
                  ->select('a.*', "b.forwarder_name", "c.consignee_name", "d.shipper_name")
                  ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                  ->join("mt_consignee as c", "a.consignee_id", "c.id")
                  ->join("mt_shipper as d", "a.shipper_id", "d.id")
                  ->where('a.id', $id)
                  ->first();
            $detail = DB::table('ex_inbound_detail')
                  ->where('job_id', $header->id)
                  ->get();
            return view("new.Export.BeaCukai.Report.inbound_pdf", compact('header', 'detail'));
      }

      private function detailShipper($shipper_id)
      {
            $data = DB::table("mt_shipper")
                  ->where('id', $shipper_id)
                  ->value('shipper_name');
            return $data;
      }

      private function detailForwarder($forwarder_id)
      {
            $data = DB::table("mt_forwarder")
                  ->where('id', $forwarder_id)
                  ->value('forwarder_name');
            return $data;
      }

      private function detailSize($size_id)
      {
            $data = DB::table("iv_container_size")
                  ->where('id', $size_id)
                  ->value('size_name');
            return $data;
      }


      private function detailOrder($job_id)
      {
            $data = DB::table("ex_outbound_order")
                  ->where('job_id', $job_id)
                  ->get();
            $data = $data->map(function ($value) {
                  $value->shipper = $this->detailShipper($value->shipper_id) ?? '-';
                  return $value;
            });
            // dd($data);
            return $data;
      }

      public function outbound($start, $end)
      {
            $tahun = explode('-', $start)[0];
            $tahun = (int) $tahun;
            if ($tahun > 2023) {
                  $data = DB::table("ex_outbound_header")
                        ->where('branch_id', $this->myBranch())
                        ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:00'])
                        ->get();
                  $data = $data->map(function ($value) {
                        $value->qty = $this->detailOrder($value->id)->sum('qty_cargo');
                        $value->order = $this->detailOrder($value->id)->first() ?? '-';
                        $value->size  = $this->detailSize($value->size_id) ?? '-';
                        return $value;
                  });
                  $list = [];
                  foreach ($data as $key => $value) {
                        $list[] = [
                              'peb_no' => $value->order->peb_no ?? '0',
                              'container_no' => $value->container_no,
                              'shipper_name' => $value->order->shipper ?? '-',
                              'stuffing_date' => \Carbon\Carbon::parse($value->created_at)->format('d-m-Y'),
                              'qty' => $value->qty,
                              'size' => $value->size,
                        ];
                  }
            } else {
                  $list = [];
            }
            return Datatables::of($list)->make(true);
      }

      private function getShipper($shipper_name)
      {
            $data = DB::table("mt_shipper")
                  ->where('shipper_name', $shipper_name)
                  ->first();
            return $data;
      }

      private function getShipperByid($shipper_id)
      {
            $data = DB::table("mt_shipper")
                  ->select('shipper_name')
                  ->where('id', $shipper_id)
                  ->value('shipper_name');
            return $data;
      }

      public function stock_report($shipper)
      {
            $data = DB::table("ex_stock_ledger")
                  ->where('status_flag', 'Inbound')
                  ->where('branch_id', $this->myBranch())
                  ->get();
            if ($shipper == 'all') {
                  $data = $data;
            } else {
                  $detailShipper = $this->getShipper($shipper);
                  $data = $data->where('shipper_id', $detailShipper->id);
            }
            $list = [];
            foreach ($data->groupBy('peb_no') as $key => $value) {
                  if ($shipper == 'all') {
                        $shipper_name = $this->getShipperByid($value->where('peb_no', $key)->first()->shipper_id) ?? '-';
                  } else {
                        $shipper_name = $detailShipper->shipper_name;
                  }

                  $receiving_date = $value->where('peb_no', $key)->first()->created_at;
                  $list[] = [
                        'peb_no' => $key,
                        'shipper_name' => $shipper_name,
                        'receiving_date' => \Carbon\Carbon::parse($receiving_date)->format('d-m-Y'),
                        'qty' =>  $value->where('peb_no', $key)->sum('quantity')
                  ];
            }
            return Datatables::of($list)->make(true);
      }

      public function downloadPDF($type, $start, $end)
      {
            if ($type == 'monthly') {
                  $data = $this->getMonthly($start, $end);
            }
            return view("new.Export.BeaCukai.Report." . $type . "_pdf", compact('data', 'end', 'start'));
      }

      public function downloadExcel($type, $start, $end)
      {
            return Excel::download(new MonthlyExport($start, $end), "Monthly-Export-Bea-Cukai.xlsx");
      }
}
