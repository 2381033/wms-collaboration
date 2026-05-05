<?php

namespace App\Http\Controllers\Transaction\CY;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

use App\Models\Transaction\CY\InvoiceHeader as CYInvoiceHeader;
use App\Models\Transaction\CY\Payment as CYPayment;
use App\Models\Transaction\CY\PaymentDetail as CYPaymentDetail;

class PaymentDetailController extends Controller
{
    public function index(Request $request) {
        if ($request->ajax()) {
            $list_data = DB::table("cy_payment_detail as a")
                            ->select("a.*", "b.confirmed_flag")
                            ->join("cy_payment as b", "a.payment_id", "b.id")
                            ->where("a.payment_id", $request->payment_id)
                            ->get();

            return datatables()->of($list_data)
                ->editColumn('invoice_amount', function ($data)
                {
                    return number_format($data->invoice_amount, 0, ".", ",");
                })
                ->editColumn("payment_amount", function ($data)
                {
                    return "<input type='hidden' value='$data->id' name='detail_id[]' class='form-control'/><input type='hidden' value='$data->invoice_id' name='invoice_id[]' class='form-control'/><input type='text' value='$data->payment_amount' name='payment_amount[]' class='form-control' style='width:200px;'/>";
                })
                ->addColumn('action', function($data){
                    $button = "";
                    if ($data->confirmed_flag == 'Open') {
                        $button .= '<button type="button" id="'.$data->id.'" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                    }
                    return $button;
                })
                ->rawColumns(["payment_amount", "action"])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function invoice(Request $request) {
        if ($request->ajax()) {
            $list_data = DB::table("cy_invoice_header as a")
                            ->select("a.*")
                            ->where("a.forwarder_payment", $request->forwarder_id)
                            ->whereColumn("a.invoice_amount", "<>", "a.payment_amount")
                            ->whereNotIn('a.id', function($query) use ($request) {
                                $query->select("invoice_id")
                                    ->from("cy_payment_detail")
                                    ->where("payment_id", $request->payment_id);
                            })
                            ->get();

            return datatables()->of($list_data)
                ->editColumn('invoice_amount', function ($data)
                {
                    return number_format($data->invoice_amount - $data->payment_amount, 0, ".", ",");
                })
                ->editColumn('job_date', function ($data)
                {
                    return date('d/m/Y', strtotime($data->job_date) );
                })
                ->editColumn("payment_amount", function ($data)
                {
                    return "<input type='hidden' value='0' name='detail_id[]' class='form-control'/><input type='hidden' value='$data->id' name='invoice_id[]' class='form-control'/><input type='text' value='0' name='payment_amount[]' class='form-control' style='width:200px;'/>";
                })
                ->rawColumns(["payment_amount"])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function store(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $payment_id = $request->payment_id;
                $detail_id = $request->detail_id;
                $invoice_id = $request->invoice_id;
                $payment_amount = $request->payment_amount;

                $payment = CYPayment::find($payment_id);

                for ($i=0; $i < count($invoice_id) ; $i++) {
                    if ($payment_amount[$i] > 0) {
                        $invoice = CYInvoiceHeader::find($invoice_id[$i]);

                        if ($detail_id[$i] == 0) {
                            $detail = new CYPaymentDetail();

                            $amount = $invoice->payment_amount + $payment_amount[$i];
                        } else {
                            $detail = CYPaymentDetail::find($detail_id[$i]);

                            $amount = $invoice->payment_amount - $detail->payment_amount + $payment_amount[$i];
                        }

                        $detail->payment_id = $payment_id;
                        $detail->invoice_id = $invoice->id;
                        $detail->invoice_no = $invoice->job_no;
                        $detail->invoice_amount = $invoice->invoice_amount;
                        $detail->payment_amount = $payment_amount[$i];
                        $detail->user_id = Auth::user()->username;
                        $detail->save();

                        $invoice->payment_amount = $amount;
                        $invoice->payment_by = $invoice->invoice_amount == $invoice->payment_amount ? Auth::user()->username : null;
                        $invoice->payment_flag = $invoice->invoice_amount == $invoice->payment_amount ? "Yes" : "No";
                        $invoice->payment_date = $invoice->invoice_amount == $invoice->payment_amount ? \Carbon\Carbon::now() : null;
                        $invoice->save();
                    }
                }

                $total_payment = CYPaymentDetail::where("payment_id", $payment_id)->sum("payment_amount");

                $payment->payment_amount = $total_payment;
                $payment->save();

                DB::commit();

                $message = ["success"=>"Sukses"];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ["error"=>$e->getMessage()];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function destroy(Request $request) {
        try {
            $detail = CYPaymentDetail::find($request->id);

            $payment = CYPayment::find($detail->payment_id);

            $payment->payment_amount = $payment->payment_amount - $detail->payment_amount;
            $payment->save();

            $invoice = CYInvoiceHeader::find($detail->invoice_id);

            $invoice->payment_flag = "No";
            $invoice->payment_by = null;
            $invoice->payment_date = null;
            $invoice->payment_amount = $invoice->payment_amount - $detail->payment_amount;
            $invoice->save();

            $detail->delete();

            $data = ['success'=>'Data successfully deleted'];
        } catch (\Illuminate\Database\QueryException $ex){
            $data = [ 'error'=> 'Cannot be deleted, this data is already used.' ];
        }

        return response()->json($data);
    }
}
