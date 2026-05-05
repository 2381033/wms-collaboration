<?php

namespace App\Http\Controllers\Transaction\Export\Outbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function stock(Request $request)
    {
        $user_id = Auth::user()->id;

        if ($request->ajax()) {
            $stock = DB::table("ex_stock_ledger as a")
                ->select(
                    "a.job_no",
                    "a.consignee_id",
                    "b.consignee_name",
                    "a.po_number",
                    "b.consignee_name",
                    "a.po_number",
                    "a.peb_no",
                    DB::raw("SUM(a.quantity) as qty_cargo"),
                    "a.total_pallet",
                    "a.quantity",
                    "a.location_code",
                    "a.aju_no",
                )
                ->join("mt_consignee as b", "a.consignee_id", "b.id")
                ->join("sm_user_branch as e", "a.branch_id", "e.branch_id")
                ->where("a.branch_id", $request->branch_id)
                ->where("a.forwarder_id", $request->forwarder_id)
                ->where("e.user_id", $user_id)
                ->where("a.status_flag", "Inbound")
                ->whereNotExists(function ($query) use ($request) {
                    $query->select(DB::raw(1))
                        ->from("ex_outbound_header as c")
                        ->join("ex_outbound_order as d", "c.id", "d.job_id")
                        ->where("a.po_number", "d.po_number")
                        ->where("a.peb_no", "d.peb_no")
                        ->where("d.status_flag", "Open")
                        ->where("c.branch_id", $request->branch_id)
                        ->where("c.forwarder_id", $request->forwarder_id);
                })
                ->groupBy(
                    "b.consignee_name",
                    "a.po_number",
                    "a.peb_no",
                    "a.total_pallet",
                )
                ->get();


            return datatables()->of($stock)
                ->addColumn('check', function ($data) {
                    return '<input type="checkbox" name="stock_id[]" class="stock-check" id="' . $data->job_no . '" value="' . $data->job_no . '">';
                })
                ->addColumn('location', function ($data) {
                    $location = '-';
                    if (!is_null($data->location_code)) {
                        $location = $data->location_code;
                    }
                    return $location;
                })
                ->rawColumns(["check", "location"])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stock = DB::table("ex_outbound_order as a")
                ->select(
                    "a.id",
                    "a.consignee_id",
                    "b.consignee_name",
                    "a.po_number",
                    "a.peb_no",
                    "a.status_flag",
                    "a.qty_cargo",
                    "c.shipper_name",
                )
                ->join("mt_consignee as b", "a.consignee_id", "b.id")
                ->join("mt_shipper as c", "a.shipper_id", "c.id")
                ->where("a.job_id", $request->job_id)
                ->get();

            $qtyDetail = DB::table('ex_outbound_detail')
                ->where('job_id', $request->job_id)
                ->where('status_flag', 'Confirmed')
                ->sum('quantity');
            $qtyOrder = $stock->sum('qty_cargo');
            return datatables()->of($stock)
                ->addColumn('action', function ($data) use ($qtyDetail, $qtyOrder) {
                    $button = "";
                    if ($qtyDetail != $qtyOrder) {
                        if ($data->status_flag == 'Open') {
                            // if (Gate::allows('gate-access', "export/outbound")) {
                            $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  id="' . $data->id . '" data-original-title="Edit" class="edit-order btn btn-info btn-sm"><i class="far fa-edit"></i></a>';
                            $button .= "&nbsp;";
                            $button .= '<button type="button" id="' . $data->id . '" class="delete-order btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                            // }
                        }
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            $user_id = Auth::user()->username;

            try {
                $data = $request->stock_id;

                $header = \App\Models\Transaction\Export\OutboundHeader::find($request->job_id);

                $qty_cargo = 0;
                $cbm = 0;
                $weight = 0;
                $total_pallet = 0;
                foreach ($data as $id) {
                    $job = \App\Models\Transaction\Export\InboundHeader::where("branch_id", $request->branch_id)->where("job_no", $id)->first();
                    $stock_list = \App\Models\Transaction\Export\InboundDetail::where("job_id", $job->id)->get();
                    foreach ($stock_list as $value) {
                        $validate_loc = \App\Models\Transaction\Export\StockLedger::where("job_no", $id)
                            ->where("branch_id", $request->branch_id)
                            ->where("po_number", $job->po_number)
                            ->where("peb_no", $job->peb_no)
                            ->whereRaw('LOWER(serial_no) = ?', [strtolower($value->serial_no)])
                            // ->where("serial_no", $value->serial_no)
                            ->pluck('location_id')->toArray();
                        $hasNullOrEmpty = collect($validate_loc)->contains(function ($item) {
                            return is_null($item) || $item === '';
                        });
                        if ($hasNullOrEmpty) {
                            throw new \Exception('validate_loc');
                        }
                    }

                    if (!$job) continue;

                    $qty_cargo += array_sum(array_map('floatval', explode('|', $job->qty_cargo)));
                    $cbm += array_sum(array_map('floatval', explode('|', $job->cbm)));
                    $weight += array_sum(array_map('floatval', explode('|', $job->weight)));
                    $total_pallet += array_sum(array_map('intval', explode('|', $job->total_pallet)));

                    $order = new \App\Models\Transaction\Export\OutboundOrder();
                    $order->job_id = $request->job_id;
                    $order->consignee_id = $job->consignee_id;
                    $order->shipper_id = $job->shipper_id;
                    $order->po_number = $job->po_number;
                    $order->peb_no = $job->peb_no;
                    $order->qty_cargo = $job->qty_cargo;
                    $order->cbm = $job->cbm;
                    $order->weight = $job->weight;
                    $order->total_pallet = $job->total_pallet;
                    $order->status_flag = "Open";
                    $order->user_id = $user_id;
                    $order->save();


                    foreach ($stock_list as $value) {
                        \App\Models\Transaction\Export\StockLedger::where("job_no", $id)
                            ->where("branch_id", $request->branch_id)
                            ->where("po_number", $job->po_number)
                            ->where("peb_no", $job->peb_no)
                            ->where("serial_no", $value->serial_no)
                            ->update([
                                'status_flag' => 'Book'
                            ]);

                        $detail = new \App\Models\Transaction\Export\OutboundDetail();

                        $detail->job_id = $request->job_id;
                        $detail->order_id = $order->id;
                        $detail->po_number = $job->po_number;
                        $detail->peb_no = $job->peb_no;
                        $detail->serial_no = $value->serial_no;
                        $detail->quantity = $value->quantity;
                        $detail->status_flag = "Open";
                        $detail->user_id = $user_id;
                        $detail->save();
                    }
                }

                $header->qty_cargo = $header->qty_cargo + $qty_cargo;
                $header->cbm = $header->cbm + $cbm;
                $header->weight = $header->weight + $weight;
                $header->total_pallet = $header->total_pallet + $total_pallet;
                $header->save();

                // DB::commit();

                $message = ['success' => 'Data Successfully Saved'];

                return $message;
            } catch (\Exception $e) {
                // DB::rollBack();

                if ($e->getMessage() === 'validate_loc') {
                    return ['validate_loc' => true];
                }

                return ['error' => $e->getMessage()];
            }
        });

        return response()->json($exception);
    }
    public function destroy(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $order = \App\Models\Transaction\Export\OutboundOrder::find($request->id);

                $header = \App\Models\Transaction\Export\OutboundHeader::find($order->job_id);

                $header->qty_cargo = $header->qty_cargo - $order->qty_cargo;
                $header->cbm = $header->cbm - $order->cbm;
                $header->weight = $header->weight - $order->weight;
                $header->total_pallet = $header->total_pallet - $order->total_pallet;
                $header->save();

                $detail = \App\Models\Transaction\Export\OutboundDetail::where('job_id', $order->job_id)
                    ->where('order_id', $order->id)
                    ->get();

                foreach ($detail as $value) {
                    $stock = \App\Models\Transaction\Export\StockLedger::where("serial_no", $value->serial_no)
                        ->where("po_number", $value->po_number)
                        ->where("peb_no", $value->peb_no)
                        ->first();

                    $stock->status_flag = "Inbound";
                    $stock->save();

                    $value->delete();
                }

                $order->delete();

                DB::commit();

                $message = ['success' => 'Data Successfully Saved'];

                return $message;
            } catch (\Exception $e) {
                DB::rollBack();

                $message = ['error' => $e->getMessage()];

                return $message;
            }
        });
        return response()->json($exception);
    }
}
