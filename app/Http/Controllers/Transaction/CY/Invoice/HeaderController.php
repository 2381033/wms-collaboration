<?php

namespace App\Http\Controllers\Transaction\CY\Invoice;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\CY\InvoiceHeader as CYInvoiceHeader;
use App\Models\Transaction\CY\InvoiceDetail as CYInvoiceDetail;
use App\Models\Transaction\CY\Outbound as CYOutbound;

class HeaderController extends Controller
{
    public $menu_name = "cy/invoice";

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

            $list_data = DB::table("cy_invoice_header as a")
                            ->select("a.*", "b.forwarder_name")
                            ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                            ->join("sm_user_branch as c", "a.branch_id", "c.branch_id")
                            ->where("c.user_id", $user_id)
                            ->where("a.branch_id", $request->branch_id)
                            ->whereBetween("a.job_date", [$date_from, $date_to])
                            ->where("a.confirmed_flag", $request->status_code)
                            ->get();

            return datatables()->of($list_data)
                ->editColumn('job_date', function ($data)
                {
                    return date('d/m/Y', strtotime($data->job_date) );
                })
                ->addColumn('job_no', function($data){
                    $button = "";
                    $button .= '<a href="' . URL("/cy/invoice/create/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                    return $button;
                })
                ->editColumn('amount', function ($data)
                {
                    return number_format($data->amount, 0, ",", ".");
                })
                ->editColumn('adm_amount', function ($data)
                {
                    return number_format($data->adm_amount, 0, ",", ".");
                })
                ->editColumn('tax_amount', function ($data)
                {
                    return number_format($data->tax_amount, 0, ",", ".");
                })
                ->editColumn('invoice_amount', function ($data)
                {
                    return number_format($data->invoice_amount, 0, ",", ".");
                })
                ->rawColumns(['job_no'])
                ->addIndexColumn()
                ->make(true);
        }

        return view("transaction.cy.invoice.index");
    }

    public function create($id = "") {
        $user_id = Auth::user()->id;

        $header = DB::table("cy_invoice_header as a")
                        ->select("a.*", "b.forwarder_name", "c.forwarder_name as payment_name")
                        ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                        ->join("mt_forwarder as c", "a.forwarder_payment", "c.id")
                        ->join("sm_user_branch as d", "a.branch_id", "d.branch_id")
                        ->where("d.user_id", $user_id)
                        ->where("a.id", $id)
                        ->first();

        $data = [
            "header" => $header
        ];

        return view("transaction.cy.invoice.create", $data);
    }

    public function store(Request $request) {
        $messsages = array(
            'branch_id.required'=>'Branch name cannot be empty.',
            'forwarder_id.required'=>'Company name cannot be empty.',
            'forwarder_payment.required'=>'Bill to company name cannot be empty.',
        );

        $rules = array(
            'branch_id' => 'required',
            'forwarder_id' => 'required',
            'forwarder_payment' => 'required',
            'adm_amount' => 'numeric',
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $exception = DB::transaction(function () use ($request) {
            try {
                $username = Auth::user()->username;
                $invoice_id = $request->invoice_id;

                $invoice = CYInvoiceHeader::find($invoice_id);

                if (!isset($invoice)) {
                    $invoice = new CYInvoiceHeader();

                    $invoice->job_no = $this->getInvoice($request->branch_id);
                    $invoice->job_date = \Carbon\Carbon::today();
                    $invoice->forwarder_id = $request->forwarder_id;
                }

                $invoice->branch_id = $request->branch_id;
                $invoice->forwarder_payment = $request->forwarder_payment;
                $invoice->adm_amount = $request->adm_amount;

                $tax_amount = 0;
                if ( $request->tax_flag == "Yes") {
                    $tax_amount = ( $invoice->amount + $request->adm_amount ) * 0.1;
                }

                $invoice_amount = $invoice->amount + $request->adm_amount + $tax_amount;

                $invoice->tax_flag = $request->tax_flag;
                $invoice->tax_amount = $tax_amount;
                $invoice->invoice_amount = $invoice_amount;
                $invoice->save();

                DB::commit();

                $message = ['success'=>url('/cy/invoice/create/' . $invoice->id)];

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

    private function getInvoice($branch_id) {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = CYInvoiceHeader::where('branch_id', $branch_id)
                            ->whereYear('job_date', $year)
                            ->whereMonth('job_date', $month)
                            ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 8, 4) + 1;
        }

        $job_no = "IV" . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $job_no;
    }

    public function print($id) {
        $header = DB::table("cy_invoice_header as a")
                    ->join("mt_forwarder as b", "a.forwarder_payment", "b.id")
                    ->where("a.id", $id)
                    ->first();

        $detail = DB::table("cy_invoice_detail as a")
                    ->join("iv_container_size as b", "a.size_id", "b.id")
                    ->where("a.invoice_id", $id)
                    ->get();

        $data = [
            "header" => $header,
            "detail" => $detail
        ];

        return view("report.cy.invoice", $data);
    }

    public function submit(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $username = Auth::user()->username;

                $invoice = CYInvoiceHeader::find($request->invoice_id);

                $invoice->confirmed_flag = "Confirmed";
                $invoice->confirmed_by = $username;
                $invoice->confirmed_date = \Carbon\Carbon::now();
                $invoice->save();

                $detail = CYInvoiceDetail::where("invoice_id", $request->invoice_id)->get();

                foreach ($detail as $value) {
                    $outbound = CYOutbound::find($value->outbound_id);

                    $outbound->invoice_flag = "Yes";
                    $outbound->invoice_date = \Carbon\Carbon::now();
                    $outbound->invoice_by = $username;
                    $outbound->save();
                }

                DB::commit();

                $message = ['success'=>url('/cy/invoice/create/' . $invoice->id)];

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
}
