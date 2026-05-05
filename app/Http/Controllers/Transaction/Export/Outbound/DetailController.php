<?php

namespace App\Http\Controllers\Transaction\Export\Outbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DetailController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stock = DB::table("ex_outbound_detail as a")
                ->where("a.order_id", $request->order_id)
                ->get();

            return datatables()->of($stock)
                ->addColumn('check', function ($data) {
                    if ($data->status_flag == "Open") {
                        return '<input type="checkbox" required="required" name="confirm_id[]" class="confirm-check" id="' . $data->id . '" value="' . $data->id . '">';
                    }

                    return "";
                })
                ->rawColumns(['check'])
                ->addIndexColumn()
                ->make(true);
        }
    }
    public function store(Request $request)
    {
        $result = DB::transaction(function () use ($request) {
            try {
                $jobId = $request->job_id;
                $orderId = $request->order_id;
                $confirmIds = $request->confirm_id;

                $job = \App\Models\Transaction\Export\OutboundHeader::findOrFail($jobId);
                $order = \App\Models\Transaction\Export\OutboundOrder::findOrFail($orderId);

                \App\Models\Transaction\Export\OutboundDetail::whereIn('id', $confirmIds)->update(['status_flag' => 'Confirmed']);

                $openDetails = \App\Models\Transaction\Export\OutboundDetail::where([
                    ['job_id', '=', $jobId],
                ])->get();

                $transactionRows = [];
                $now = now();

                foreach ($openDetails as $value) {
                    $transactionRows[] = [
                        'job_type' => 'out',
                        'branch_id' => $job->branch_id,
                        'job_no' => $job->job_no,
                        'peb_no' => $value->peb_no,
                        'vehicle_no' => $job->container_no,
                        'forwarder_id' => $job->forwarder_id,
                        'shipper_id' => $order->shipper_id,
                        'consignee_id' => $order->consignee_id,
                        'created_at' => $now,
                        'serial_no' => $value->serial_no,
                        'cbm' => $job->cbm,
                        'quantity' => $value->quantity,
                        'total_pallet' => $job->total_pallet,
                        'pallet_id' => $value->pallet_id,
                        'weight' => $job->weight,
                        'user_id' => $value->user_id,
                    ];
                }

                if (!empty($transactionRows)) {
                    DB::table('ex_stock_transaction')->insert($transactionRows);
                }

                $stillOpen = \App\Models\Transaction\Export\OutboundDetail::where([
                    ['job_id', '=', $jobId],
                    ['order_id', '=', $orderId],
                    ['status_flag', '=', 'Open'],
                ])->exists(); // lebih efisien dari count()

                if (!$stillOpen) {
                    $order->update(['status_flag' => 'Full']);
                }

                $openOrdersExist = \App\Models\Transaction\Export\OutboundOrder::where([
                    ['job_id', '=', $jobId],
                    ['status_flag', '=', 'Open'],
                ])->exists();

                if (!$openOrdersExist) {
                    $job->update(['status_flag' => 'Confirmed']);
                }

                return ['success' => 'Data Successfully Saved'];
            } catch (\Exception $e) {
                throw $e; // transaction otomatis rollback
            }
        });

        return response()->json($result);
    }


    public function pickingList($id)
    {
        $header = DB::table('ex_outbound_header')
            ->where('id', $id)
            ->first();
        $forwarder = DB::table('mt_forwarder')->where('id', $header->forwarder_id)->value('forwarder_name') ?? '-';
        $detail = DB::table('ex_outbound_detail')
            ->where('job_id', $id)
            ->get();
        $order = DB::table('ex_outbound_order')
            ->where('job_id', $id)
            ->get();
        // dd($detail, $order);
        $detail = $detail->map(function ($value) use ($order) {
            $masterShipper = $order->where('id', $value->order_id)->first()->shipper_id;
            $masterConsignee = $order->where('id', $value->order_id)->first()->consignee_id;
            $value->location_code = DB::table('ex_stock_ledger')
                ->select('location_code')
                ->where('serial_no', $value->serial_no)
                ->value('location_code') ?? '-';
            $value->shipper = DB::table('mt_shipper')->where('id', $masterShipper)->value('shipper_name') ?? '-';
            $value->consignee = DB::table('mt_consignee')->where('id', $masterConsignee)->value('consignee_name') ?? '-';
            return $value;
        });
        $order = $detail;
        return view('transaction.export.outbound.picking', compact('order', 'header', 'forwarder'));
    }
}
