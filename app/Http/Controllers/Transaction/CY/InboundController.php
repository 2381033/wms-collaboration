<?php

namespace App\Http\Controllers\Transaction\CY;

use App\Helpers\GlobalHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Transaction\CY\Inbound as CYInbound;
use App\Models\Transaction\CY\StockLedger as CYStockLedger;
use App\Models\Transaction\CY\StockTransaction as CYStockTransaction;

class InboundController extends Controller
{
    public $menu_name = "cy/inbound";

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

            $list_data = DB::table("cy_inbound as a")
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
                    $button .= '<a href="' . URL("/cy/inbound/view/$data->id") . '" class="btn btn-default btn-sm"><i class="far fa-file"></i> ' . $data->job_no . '</a>';
                    return $button;
                })
                ->rawColumns(['job_no'])
                ->addIndexColumn()
                ->make(true);
        }

        return view("transaction.cy.inbound.index");
    }

    public function view($id = "") {
        $header = DB::table("cy_inbound as a")
                        ->select("a.*", "b.forwarder_name", "c.size_name", "d.type_name", "e.type_name as invoice_name")
                        ->join("mt_forwarder as b", "a.forwarder_id", "b.id")
                        ->leftjoin("iv_container_size as c", "a.size_id", "c.id")
                        ->leftjoin("iv_container_type as d", "a.type_id", "d.id")
                        ->leftjoin("cy_invoice_type as e", "a.invoice_type", "e.id")
                        ->where("a.id", $id)
                        ->first();

        $data = [
            "header" => $header,
        ];

        return view("transaction.cy.inbound.view", $data);
    }

    public function store(Request $request) {
        $exception = DB::transaction(function () use ($request) {
            try {
                $username = Auth::user()->username;

                $header = CYInbound::find($request->inbound_id);

                $header->confirmed_flag = "Confirmed";
                $header->confirmed_date = \Carbon\Carbon::today();
                $header->confirmed_by = $username;
                $header->save();

                $ledger = new CYStockLedger();

                $serial_no = $this->getSerial($header->branch_id);

                $ledger->branch_id = $header->branch_id;
                $ledger->forwarder_id = $header->forwarder_id;
                $ledger->booking_id = $header->booking_id;
                $ledger->inbound_id = $request->inbound_id;
                $ledger->booking_no = $header->booking_no;
                $ledger->serial_no = $serial_no;
                $ledger->job_no = $header->job_no;
                $ledger->job_date = \Carbon\Carbon::today();
                $ledger->invoice_type = $header->invoice_type;
                $ledger->reference_no = $header->reference_no;
                $ledger->vehicle_no = $header->vehicle_no;
                $ledger->driver_name = $header->driver_name;
                $ledger->size_id = $header->size_id;
                $ledger->type_id = $header->type_id;
                $ledger->container_status = $header->container_status;
                $ledger->container_no = $header->container_no;
                $ledger->qtys = 1;
                $ledger->qtya = 1;
                $ledger->qtyp = 0;
                $ledger->user_id = $username;
                $ledger->save();

                $transaction = new CYStockTransaction();

                $transaction->branch_id = $header->branch_id;
                $transaction->forwarder_id = $header->forwarder_id;
                $transaction->booking_id = $header->booking_id;
                $transaction->inbound_id = $request->inbound_id;
                $transaction->booking_no = $header->booking_no;
                $transaction->serial_no = $serial_no;
                $transaction->job_no = $header->job_no;
                $transaction->job_date = \Carbon\Carbon::today();
                $transaction->job_type = "Inbound";
                $transaction->invoice_type = $header->invoice_type;
                $transaction->reference_no = $header->reference_no;
                $transaction->vehicle_no = $header->vehicle_no;
                $transaction->driver_name = $header->driver_name;
                $transaction->size_id = $header->size_id;
                $transaction->type_id = $header->type_id;
                $transaction->container_status = $header->container_status;
                $transaction->container_no = $header->container_no;
                $transaction->qty = 1;
                $transaction->user_id = $username;
                $transaction->reference_job = $header->job_no;
                $transaction->save();

                $stock = CYStockLedger::where("booking_id", $header->booking_id)->count();

                if ( $stock == 0 ) {
                    DB::rollBack();

                    $message = ['error'=>["Container has not been received"]];

                    return $message;
                }

                DB::commit();

                $message = ["success"=>"Sukses"];

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

    private function getSerial($branch_id) {
        $job_date = \Carbon\Carbon::today();

        $year = $job_date->year;
        $month = $job_date->month;

        $serial = CYStockLedger::where('branch_id', $branch_id)
                            ->whereYear('job_date', $year)
                            ->whereMonth('job_date', $month)
                            ->max("serial_no");

        if (is_null($serial)) {
            $increment = 1;
        } else {
            $increment = substr($serial, 6, 4) + 1;
        }

        $serial_no = $year . Str::of($month)->padLeft(2, '0') . Str::of($increment)->padLeft(4, '0');

        return $serial_no;
    }
}
