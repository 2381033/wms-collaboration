<?php

namespace App\Http\Controllers\Transaction\CY;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\CY\InvoiceHeader as CYInvoiceHeader;
use App\Models\Transaction\CY\Payment as CYPayment;

class PaymentController extends Controller
{
    public $menu_name = "cy/payment";

    public function index(Request $request) {
        if (!GlobalHelpers::isAccess($this->menu_name)) {
            abort(403);
        }

        $user_id = Auth::user()->id;

        if ($request->ajax()) {
            $dateObject = \Carbon\Carbon::createFromFormat("d/m/Y", $request->date_from);
            $date_from = \Carbon\Carbon::parse($dateObject)->format("Y-m-d");

            $dateObject = \Carbon\Carbon::createFromFormat("d/m/Y", $request->date_to);
            $date_to = \Carbon\Carbon::parse($dateObject)->format("Y-m-d");

            $list_data = DB::table("cy_payment as a")
                            ->select("a.*", "b.forwarder_name")
                            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                            ->join("sm_user_branch as c", "a.branch_id", "c.branch_id")
                            ->where("c.user_id", $user_id)
                            ->where("a.branch_id", $request->branch_id)
                            ->whereBetween("a.job_date", [$date_from, $date_to])
                            ->where("a.confirmed_flag", $request->status_code)
                            ->get();

            return datatables()->of($list_data)
                ->editColumn('payment_date', function ($data)
                {
                    return date('d/m/Y', strtotime($data->payment_date) );
                })
                ->editColumn('job_date', function ($data)
                {
                    return date('d/m/Y', strtotime($data->job_date) );
                })
                ->addColumn('job_no', function($data){
                    $button = "";
                    $button .= '<a href="' . URL("/cy/payment/create/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                    return $button;
                })
                ->rawColumns(['job_no'])
                ->addIndexColumn()
                ->make(true);
        }

        return view("transaction.cy.bill.index");
    }

    public function create($id = "") {
        $user_id = Auth::user()->id;

        $header =  DB::table("cy_payment as a")
                        ->select("a.*", "b.forwarder_name")
                        ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                        ->join("sm_user_branch as c", "a.branch_id", "c.branch_id")
                        ->where("c.user_id", $user_id)
                        ->where("a.id", $id)
                        ->first();

        $data = [
            "header" => $header
        ];

        return view("transaction.cy.bill.create", $data);
    }

    public function store(Request $request) {
        $messsages = array(
            'branch_id.required'=>'Branch name cannot be empty.',
            'forwarder_id.required'=>'Company name cannot be empty.',
            // 'payment_amount.required'=>'Payment amount cannot be empty.',
            'payment_date.required'=>'Payment date cannot be empty.',
        );

        $rules = array(
            'branch_id' => 'required',
            'forwarder_id' => 'required',
            // 'payment_amount' => 'required|numeric',
            'payment_date' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            try {
                $username = Auth::user()->username;
                $payment_id = $request->payment_id;

                $header = CYPayment::find($payment_id);

                if (!isset($header)) {
                    $header = new CYPayment();

                    $header->job_no = $this->getJob($request->branch_id);
                    $header->job_date = \Carbon\Carbon::today();
                }

                $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->payment_date);
                $payment_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

                $header->branch_id = $request->branch_id;
                $header->forwarder_id = $request->forwarder_id;
                $header->payment_date = $payment_date;
                $header->user_id = $username;
                $header->save();

                DB::commit();

                $message = ['success'=>url('/cy/payment/create/' . $header->id)];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    public function submit(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $username = Auth::user()->username;

                $payment = CYPayment::find($request->payment_id);

                $invoice = CYInvoiceHeader::find($request->invoice_id);

                // if ( $invoice->invoice_amount == $payment->payment_amount ) {
                    $invoice->payment_flag = 'Yes';
                    $invoice->payment_by = $username;
                    $invoice->payment_date = \Carbon\Carbon::now();
                // }

                $invoice->payment_amount = $invoice->payment_amount + $payment->payment_amount;
                $invoice->save();

                $payment->confirmed_flag = "Confirmed";
                $payment->confirmed_by = $username;
                $payment->confirmed_date = \Carbon\Carbon::now();
                $payment->save();

                DB::commit();

                $message = ['success'=>url('/cy/payment/create/' . $payment->id)];

                return $message;
            }
            catch(\Exception $e) {
                DB::rollBack();

                $message = ['error'=>[$e->getMessage()]];

                return $message;
            }
        });

        return response()->json($exception);
    }

    private function getJob($branch_id) {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = CYPayment::where('branch_id', $branch_id)
                    ->whereYear('job_date', $year)
                    ->whereMonth('job_date', $month)
                    ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }

        $job_no = 'P' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $job_no;
    }
}
