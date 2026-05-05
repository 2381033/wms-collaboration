<?php

namespace App\Http\Controllers\Transaction\CY\Invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Transaction\CY\InvoiceHeader as CYInvoiceHeader;
use App\Models\Transaction\CY\InvoiceDetail as CYInvoiceDetail;
use App\Models\Transaction\CY\Outbound as CYOutbound;

class DetailController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $list_data = DB::table("cy_invoice_detail as a")
                            ->where("a.invoice_id", $request->invoice_id)
                            ->get();

            return datatables()->of($list_data)
                ->editColumn('received_date', function ($data)
                {
                    return date('d/m/Y', strtotime($data->received_date) );
                })
                ->editColumn('dispatch_date', function ($data)
                {
                    return date('d/m/Y', strtotime($data->dispatch_date) );
                })
                ->editColumn('lolo_amount', function ($data)
                {
                    return number_format($data->lolo_amount, 0, ",", ".");
                })
                ->editColumn('storage_amount', function ($data)
                {
                    return number_format($data->storage_amount, 0, ",", ".");
                })
                ->editColumn('total_amount', function ($data)
                {
                    return number_format($data->total_amount, 0, ",", ".");
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function getOutboundList(Request $request) {
        if ($request->ajax()) {
            $list_data = DB::table("cy_outbound as a")
                            ->select("a.*")
                            ->join("cy_invoice_type as b", "a.invoice_type", "b.id")
                            ->where("a.forwarder_id", $request->forwarder_id)
                            ->where("b.invoice_flag", "No")
                            ->where("a.confirmed_flag", "Confirmed")
                            ->where("a.invoice_flag", "No")
                            ->get();

            return datatables()->of($list_data)
                ->editColumn('received_date', function ($data)
                {
                    return date('d/m/Y', strtotime($data->received_date) );
                })
                ->editColumn('dispatch_date', function ($data)
                {
                    return date('d/m/Y', strtotime($data->dispatch_date) );
                })
                ->editColumn('lolo_amount', function ($data)
                {
                    return number_format($data->lolo_amount, 0, ",", ".");
                })
                ->editColumn('storage_amount', function ($data)
                {
                    return number_format($data->storage_amount, 0, ",", ".");
                })
                ->editColumn('total_amount', function ($data)
                {
                    return number_format($data->total_amount, 0, ",", ".");
                })
                ->addColumn('check', function ($data) {
                    return '<input type="checkbox" name="outbound_id[]" class="out-check" id="' . $data->id . '" value="' . $data->id . '">';
                })
                ->rawColumns(["check"])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function store(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            $company_id = Auth::user()->company_id;
            $user_id = Auth::user()->username;

            try {
                $data = $request->outbound_id;

                $invoice = CYInvoiceHeader::find($request->invoice_id);

                $total_amount = 0;
                foreach ($data as $id) {
                    $outbound = CYOutbound::find($id);

                    $detail = new CYInvoiceDetail();

                    $detail->invoice_id = $request->invoice_id;
                    $detail->outbound_id = $id;
                    $detail->outbound_no = $outbound->job_no;
                    $detail->job_no = $invoice->job_no;
                    $detail->serial_id = $outbound->serial_id;
                    $detail->serial_no = $outbound->serial_no;
                    $detail->size_id = $outbound->size_id;
                    $detail->container_no = $outbound->container_no;
                    $detail->received_date = $outbound->received_date;
                    $detail->dispatch_date = $outbound->dispatch_date;
                    $detail->leadtime = $outbound->leadtime;
                    $detail->lolo_amount = $outbound->lolo_amount;
                    $detail->storage_amount = $outbound->storage_amount;
                    $detail->total_amount = $outbound->total_amount;
                    $detail->save();

                    $total_amount = $total_amount + $outbound->total_amount;

                    $outbound->invoice_no = $invoice->job_no;
                    $outbound->invoice_flag = "Yes";
                    $outbound->invoice_by = $user_id;
                    $outbound->invoice_date = \Carbon\Carbon::now();
                    $outbound->save();
                }

                $tax_amount = 0;
                $adm_amount = $invoice->adm_amount;
                $amount = $invoice->amount + $total_amount;
                if ( $invoice->tax_flag == "Yes" ) {
                    $tax_amount = ( $amount + $adm_amount ) * 0.1;
                }

                $invoice_amount = $amount + $tax_amount + $adm_amount;

                $invoice->amount = $amount;
                $invoice->tax_amount = $tax_amount;
                $invoice->invoice_amount = $invoice_amount;
                $invoice->save();

                DB::commit();

                $message = ['success'=>'Data Successfully Saved'];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>$e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }
}
