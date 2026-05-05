<?php

namespace App\Http\Controllers\Transaction\Export\Outbound;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

use App\Models\Transaction\Export\InboundHeader as ExportInboundHeader;
use App\Models\Transaction\Export\OutboundHeader as ExportOutboundHeader;
use App\Models\Transaction\Export\InboundDetail as ExportInboundDetail;
use App\Models\Transaction\Export\StockLedger as ExportStockLedger;
use App\Models\Transaction\Export\OutboundOrder as ExportOutboundOrder;
use App\Models\Transaction\Export\OutboundDetail as ExportOutboundDetail;

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
                    "a.peb_no",
                    "a.qty_cargo",
                    "a.aju_no",
                    "a.total_pallet"
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
                    "a.job_no",
                    "a.consignee_id",
                    "b.consignee_name",
                    "a.po_number",
                    "a.peb_no",
                    "a.qty_cargo",
                    "a.total_pallet"
                )
                ->get();

            return datatables()->of($stock)
                ->addColumn('check', function ($data) {
                    return '<input type="checkbox" name="stock_id[]" class="stock-check" id="' . $data->job_no . '" value="' . $data->job_no . '">';
                })
                ->rawColumns(["check"])
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
                    "a.qty_cargo",
                    "a.consignee_id",
                    "b.consignee_name",
                    "a.po_number",
                    "a.peb_no",
                    // "a.aju_no",
                    "a.status_flag",
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

                $header = ExportOutboundHeader::find($request->job_id);

                $qty_cargo = 0;
                $cbm = 0;
                $weight = 0;
                $total_pallet = 0;
                foreach ($data as $id) {
                    $job = ExportInboundHeader::where("branch_id", $request->branch_id)->where("job_no", $id)->first();

                    $qty_cargo = $qty_cargo + $job->qty_cargo;
                    $cbm = $cbm + $job->cbm;
                    $weight = $weight + $job->weight;
                    $total_pallet = $total_pallet + $job->total_pallet;

                    $order = new ExportOutboundOrder();
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

                    $stock_list = ExportInboundDetail::where("job_id", $job->id)->get();

                    foreach ($stock_list as $value) {
                        $stock = ExportStockLedger::where("job_no", $id)
                            ->where("branch_id", $request->branch_id)
                            ->where("po_number", $job->po_number)
                            ->where("peb_no", $job->peb_no)
                            ->where("serial_no", $value->serial_no)
                            ->first();

                        $stock->status_flag = "Book";
                        $stock->save();

                        $detail = new ExportOutboundDetail();

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

    public function destroy(Request $request)
    {
        $exception = DB::transaction(function () use ($request) {
            try {
                $order = ExportOutboundOrder::find($request->id);

                $header = ExportOutboundHeader::find($order->job_id);

                $header->qty_cargo = $header->qty_cargo - $order->qty_cargo;
                $header->cbm = $header->cbm - $order->cbm;
                $header->weight = $header->weight - $order->weight;
                $header->total_pallet = $header->total_pallet - $order->total_pallet;
                $header->save();

                $detail = ExportOutboundDetail::where('job_id', $order->job_id)
                    ->where('order_id', $order->id)
                    ->get();

                foreach ($detail as $value) {
                    $stock = ExportStockLedger::where("serial_no", $value->serial_no)
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
