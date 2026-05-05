<?php

namespace App\Http\Controllers\Transaction\CY;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\CY\InvoiceHeader as CYInvoiceHeader;
use App\Models\Transaction\CY\InvoiceDetail as CYInvoiceDetail;
use App\Models\Transaction\CY\Outbound as CYOutbound;
use App\Models\Transaction\CY\ChecklistHeader as CYChecklistHeader;
use App\Models\Transaction\CY\Checklist as CYChecklist;
use App\Models\Transaction\CY\ChecklistDetail as CYChecklistDetail;
use App\Models\Transaction\CY\StockLedger as CYStockLedger;
use App\Models\Transaction\CY\StockTransaction as CYStockTransaction;
use App\Models\Master\InvoiceType as MasterInvoiceType;
use App\Models\Master\Export\Forwarder as ExportForwarder;

class OutboundController extends Controller
{
    public $menu_name = "cy/outbound";

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

            $list_data = DB::table("cy_outbound as a")
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
                    $button .= '<a href="' . URL("/cy/outbound/create/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                    return $button;
                })
                ->rawColumns(['job_no'])
                ->addIndexColumn()
                ->make(true);
        }

        return view("transaction.cy.outbound.index");
    }

    public function create($id = "") {
        $user_id = Auth::user()->id;

        $header =  DB::table("cy_outbound as a")
                        ->select("a.*", "b.forwarder_name", "c.rate_amount", "b.storage_amount as storage", "d.free_storage", "f.payment_date")
                        ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                        ->join("mt_forwarder_size as c", function ($join) {
                            $join->on("a.forwarder_id", "c.forwarder_id")
                                  ->on("a.size_id", "c.size_id");
                        })
                        ->join("cy_invoice_type as d", "a.invoice_type", "d.id")
                        ->join("sm_user_branch as e", "a.branch_id", "e.branch_id")
                        ->leftJoin("cy_invoice_header as f", "a.invoice_no", "f.job_no")
                        ->where("e.user_id", $user_id)
                        ->where("a.id", $id)
                        ->first();

        $data = [
            "header" => $header
        ];

        return view("transaction.cy.outbound.create", $data);
    }

    public function store(Request $request) {
        $messsages = array(
            'branch_id.required'=>'Branch name cannot be empty.',
            'forwarder_id.required'=>'Forwarder name cannot be empty.',
            'driver_name.required'=>'Driver name cannot be empty.',
            'vehicle_no.required'=>'Vehicle No cannot be empty.',
            'container_no.required'=>'Container No cannot be empty.',
            'dispatch_date.required'=>'Dispatch Date cannot be empty.',
        );

        $rules = array(
            'branch_id' => 'required',
            'forwarder_id' => 'required',
            'driver_name' => 'required',
            'vehicle_no' => 'required',
            'container_no' => 'required',
            'dispatch_date' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules,$messsages);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->received_date);
        $received_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->dispatch_date);
        $dispatch_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

        if ( $dispatch_date < $received_date ) {
            return response()->json(['error'=>["Dispatch date must be greater than received date!!!"]]);
        }

        $exception = DB::transaction(function () use ($request) {
            try {
                $username = Auth::user()->username;
                $outbound_id = $request->outbound_id;

                $outbound = CYOutbound::find($outbound_id);

                if (!isset($outbound)) {
                    $job_no = $this->getJob($request->branch_id);
                    $outbound = new CYOutbound();

                    $outbound->job_no = $job_no;
                    $outbound->job_date = \Carbon\Carbon::today();
                } else {
                    $job_no = $outbound->job_no;
                }

                $stock = CYStockLedger::find($request->serial_id);

                $stock->qtyp = 1;
                $stock->qtya = 0;
                $stock->save();

                $dateObject = \Carbon\Carbon::createFromFormat('d/m/Y', $request->dispatch_date);
                $dispatch_date = \Carbon\Carbon::parse($dateObject)->format('Y-m-d');

                $invoice_type = MasterInvoiceType::find($stock->invoice_type);

                $check_no = $this->getCheckList($request->branch_id);

                $check_header = new CYChecklistHeader();
                $check_header->branch_id = $request->branch_id;
                $check_header->forwarder_id = $stock->forwarder_id;
                $check_header->job_no = $check_no;
                $check_header->job_date = \Carbon\Carbon::today();
                $check_header->job_type = "Outbound";
                $check_header->driver_name = $request->driver_name;
                $check_header->vehicle_no = $request->vehicle_no;
                $check_header->size_id = $stock->size_id;
                $check_header->type_id = $stock->type_id;
                $check_header->container_status = $stock->container_status;
                $check_header->container_no = $stock->container_no;
                $check_header->save();

                $check_list = CYChecklist::where("active", "Yes")->get();

                foreach ($check_list as $value) {
                    $check_detail = new CYChecklistDetail();
                    $check_detail->checklist_id = $check_header->id;
                    $check_detail->check_id = $value->id;
                    $check_detail->save();
                }

                $forwarder =  ExportForwarder::find($stock->forwarder_id);

                $adm_amount = $forwarder->adm_amount;
                $total_amount = $request->total_amount + $adm_amount;
                $tax_amount = $total_amount * 0.1;
                $invoice_amount = $request->total_amount + $adm_amount + $tax_amount;

                $storage_amount = $request->leadtime * $forwarder->storage_amount;

                $outbound->branch_id = $request->branch_id;
                $outbound->forwarder_id = $request->forwarder_id;
                $outbound->serial_id = $request->serial_id;
                $outbound->serial_no = $stock->serial_no;
                $outbound->checklist_no = $check_no;
                $outbound->invoice_type = $stock->invoice_type;
                $outbound->driver_name = $request->driver_name;
                $outbound->vehicle_no = $request->vehicle_no;
                $outbound->size_id = $stock->size_id;
                $outbound->type_id = $stock->type_id;
                $outbound->container_no = $request->container_no;
                $outbound->received_date = $stock->job_date;
                $outbound->dispatch_date = $dispatch_date;
                $outbound->leadtime = $request->leadtime;
                $outbound->lolo_amount = $request->lolo_amount;
                $outbound->storage_amount = $storage_amount;
                $outbound->total_amount = $request->total_amount;
                $outbound->user_id = $username;
                $outbound->save();

                if ( $invoice_type->invoice_flag == "Yes" ) {
                    $invoice_no = $this->getInvoice($request->branch_id);

                    $check = CYInvoiceDetail::where("outbound_no", $outbound->job_no)->count();

                    if ($check == 0) {
                        $invoice_header = new CYInvoiceHeader();
                        $invoice_detail = new CYInvoiceDetail();
                    } else {
                        $invoice_detail = CYInvoiceDetail::where("outbound_no", $outbound->job_no)->first();

                        $invoice_header = CYInvoiceHeader::find($invoice_detail->invoice_id);
                    }

                    $invoice_header->branch_id = $stock->branch_id;
                    $invoice_header->job_no = $invoice_no;
                    $invoice_header->job_date = \Carbon\Carbon::today();
                    $invoice_header->forwarder_id = $stock->forwarder_id;
                    $invoice_header->forwarder_payment = $stock->forwarder_id;
                    $invoice_header->amount = $request->total_amount;
                    $invoice_header->adm_amount = $adm_amount;
                    $invoice_header->tax_flag = "Yes";
                    $invoice_header->tax_amount = $tax_amount;
                    $invoice_header->invoice_amount = $invoice_amount;
                    $invoice_header->review_flag = "No";
                    $invoice_header->save();

                    $invoice_detail->invoice_id = $invoice_header->id;
                    $invoice_detail->outbound_id = $outbound->id;
                    $invoice_detail->outbound_no = $outbound->job_no;
                    $invoice_detail->job_no = $invoice_header->job_no;
                    $invoice_detail->serial_id = $stock->id;
                    $invoice_detail->serial_no = $stock->serial_no;
                    $invoice_detail->size_id = $stock->size_id;
                    $invoice_detail->container_no = $stock->container_no;
                    $invoice_detail->received_date = $outbound->received_date;
                    $invoice_detail->dispatch_date = $outbound->dispatch_date;
                    $invoice_detail->leadtime = $outbound->leadtime;
                    $invoice_detail->lolo_amount = $outbound->lolo_amount;
                    $invoice_detail->storage_amount = $outbound->storage_amount;
                    $invoice_detail->total_amount = $outbound->total_amount;
                    $invoice_detail->save();

                    $outbound->invoice_no = $invoice_no;
                    $outbound->save();
                }

                DB::commit();

                $message = ['success'=>url('/cy/outbound/create/' . $outbound->id)];

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

        $job = CYOutbound::where('branch_id', $branch_id)
                            ->whereYear('job_date', $year)
                            ->whereMonth('job_date', $month)
                            ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 6, 4) + 1;
        }

        $job_no = $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $job_no;
    }

    private function getCheckList($branch_id) {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $job = CYChecklistHeader::where('branch_id', $branch_id)
                            ->whereYear('job_date', $year)
                            ->whereMonth('job_date', $month)
                            ->where("job_type", "Outbound")
                            ->max("job_no");

        if (is_null($job)) {
            $increment = 1;
        } else {
            $increment = substr($job, 7, 4) + 1;
        }

        $job_no = 'O' . $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $job_no;
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

    public function submit(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $username = Auth::user()->username;

                $header = CYOutbound::find($request->outbound_id);

                $invoice = CYInvoiceHeader::where("job_no", $header->invoice_no)->first();

                $invoice_type = MasterInvoiceType::find($header->invoice_type);

                if ( $invoice_type->invoice_flag == "Yes" ) {
                    if ( $invoice->payment_flag == "No" ) {
                        DB::rollBack();

                        $message = ['error'=>["Invoice process has not been done!!!"]];

                        return $message;
                    }
                }

                $header->confirmed_flag = "Confirmed";
                $header->confirmed_by = $username;
                $header->confirmed_date = \Carbon\Carbon::now();
                $header->save();

                $stock = CYStockLedger::find($header->serial_id);

                $stock->qtys = 0;
                $stock->qtyp = 0;
                $stock->save();

                $transaction = new CYStockTransaction();

                $transaction->branch_id = $stock->branch_id;
                $transaction->forwarder_id = $stock->forwarder_id;
                $transaction->booking_id = $stock->booking_id;
                $transaction->inbound_id = $stock->inbound_id;
                $transaction->booking_no = $stock->booking_no;
                $transaction->job_no = $stock->job_no;
                $transaction->job_date = \Carbon\Carbon::today();
                $transaction->job_type = "Outbound";
                $transaction->invoice_type = $stock->invoice_type;
                $transaction->serial_no = $stock->serial_no;
                $transaction->reference_no = $stock->reference_no;
                $transaction->vehicle_no = $header->vehicle_no;
                $transaction->driver_name = $header->driver_name;
                $transaction->size_id = $stock->size_id;
                $transaction->type_id = $stock->type_id;
                $transaction->container_status = $stock->container_status;
                $transaction->container_no = $stock->container_no;
                $transaction->qty = 1;
                $transaction->reference_job = $header->job_no;
                $transaction->user_id = $username;
                $transaction->save();

                DB::commit();

                $message = ['success'=>url('/cy/outbound/create/' . $header->id)];

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
